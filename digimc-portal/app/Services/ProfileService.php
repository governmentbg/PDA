<?php

namespace App\Services;

use App\Mail\PasswordChangedMail;
use App\Mail\ProfileUpdatedMail;
use App\Models\CulturalObject;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class ProfileService
{
    public function updateProfile(Request $request): void
    {
        if (!Auth::check()) {
            throw new \Exception('no user logged in', 1);
        }

        $user = Auth()->user();

        $before = $user->only([
            'first_name',
            'last_name',
            'profile_image_path',
            'wants_notifications',
            'subscribed_news',
            'subscribed_weekly',
        ]);

        $oldPath = $user->profile_image_path;
        $newPath = null;

        if ($photo = $request->file('profile_image_path')) {
            $relDir = "uploads/avatars/{$user->id}";
            $absDir = public_path($relDir);

            if (!File::exists($absDir)) {
                File::makeDirectory($absDir, 0775, true);
            }

            if (!is_writable($absDir)) {
                throw new \RuntimeException("Upload directory is not writable: {$absDir}");
            }

            $ext = $photo->extension();
            $filename = sha1_file($photo->getRealPath()) . '.' . $ext;

            $photo->move($absDir, $filename);

            $newPath = "{$relDir}/{$filename}";
            $user->profile_image_path = $newPath;
        }

// Text fields
        if ($request->filled('first_name')) {
            $user->first_name = trim($request->input('first_name'));
        }

        if ($request->filled('last_name')) {
            $user->last_name = trim($request->input('last_name'));
        }

// Toggles
        $user->wants_notifications = $request->boolean('wants_notifications');
        $user->subscribed_news = $request->boolean('subscribed_news');
        $user->subscribed_weekly = $request->boolean('subscribed_weekly');

        $user->save();

// Delete old photo
        if ($newPath && $oldPath && $oldPath !== $newPath) {
            File::delete(public_path($oldPath));
        }

        $after   = $user->only(array_keys($before));
        $changed = array_diff_assoc($after, $before);

        if ($changed === []) {
            return;
        }

        $labels = [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'profile_image_path' => 'Profile photo',
            'wants_notifications' => 'Email notifications',
            'subscribed_news' => 'News subscription',
            'subscribed_weekly' => 'Weekly newsletter',
        ];

        $fmt = function (string $key, $value): string {
            if ($key === 'profile_image_path') {
                return '';
            }
            if (in_array($key, ['wants_notifications', 'subscribed_news', 'subscribed_weekly'], true)) {
                return $value ? 'Yes' : 'No';
            }
            return ($value === null || $value === '') ? '—' : (string)$value;
        };

        $formatted = [];
        foreach ($changed as $key => $newVal) {
            $formatted[] = [
                'key' => $key,
                'label' => $labels[$key],
                'old' => $fmt($key, $before[$key]),
                'new' => $fmt($key, $newVal),
            ];
        }

        $updateDate = Carbon::now()->format('d.m.Y \в H:i');

        if ($user->wants_notifications) {
            Mail::to($user->email)
                ->locale($user->locale ?? app()->getLocale())
                ->send(new ProfileUpdatedMail($user, $formatted, $updateDate));
        }

    }

    public function updatePassword($newPassword): void
    {
        if(!Auth::check()) {
            throw new \Exception('no user logged in', 1);
        }

        $user = Auth()->user();
        $user->password = bcrypt($newPassword);
        $user->save();

        // Send email
        if ($user->wants_notifications) {
            Mail::to($user->email)
                ->locale($user->locale ?? app()->getLocale())
                ->send(new PasswordChangedMail($user));
        }
    }

    public function getUserFavoritesPaginated(int $perPage = 15): LengthAwarePaginator
    {
        $user = Auth::user();

        if (is_null($user)) {
            throw new \Exception('User not authenticated');
        }

        $likes = $user->likes()
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $likedObjectIds = $likes->pluck('cultural_object_id')->toArray();

        $favoriteObjects = CulturalObject::whereIn('id', $likedObjectIds)
            ->with('provider')
            ->get()
            ->sortBy(fn($object) => array_search($object->id, $likedObjectIds));

        /** @var \Illuminate\Support\Collection<\Illuminate\Database\Eloquent\Model> $baseCollection */
        $baseCollection = collect($favoriteObjects);

        $likes->setCollection($baseCollection);

        return $likes;
    }

    public function addFavorites(array $objectIds, $user): array
    {
        foreach ($objectIds as $object_id) {
            if (!$user->likes()->where('cultural_object_id', $object_id)->exists()) {
                $user->likes()->create(['cultural_object_id' => $object_id]);
            }
        }

        return $objectIds;
    }

    public function removeFavorites(array $objectIds, $user): array
    {
        $likes = $user->likes()->whereIn('cultural_object_id', $objectIds)->get();

        foreach ($likes as $like) {
            $like->delete();
        }

        return $objectIds;
    }


}
