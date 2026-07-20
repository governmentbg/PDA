<?php

namespace App\Services;

use App\Enums\GalleryEnum;
use App\Enums\SettingEnum;
use App\Mail\GalleryApprovedMail;
use App\Mail\GalleryPublishRequestedMail;
use App\Mail\GalleryStatusUpdateMail;
use App\Models\CulturalObject;
use App\Models\Gallery;
use App\Models\GalleryCulturalObject;
use App\Models\User;
use App\Models\WebResource;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Mail;

class GalleryService
{
    public function listForUser()
    {
        $user = Auth::user();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $perPage = SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH);

        $all = Gallery::where('user_id', $user->id)->orderBy('id', 'DESC')->get();

        $public = Gallery::where('user_id', $user->id)
            ->where('status', GalleryEnum::STATUS_PUBLIC)
            ->orderBy('updated_at', 'DESC')
            ->paginate($perPage, ['*'], 'public_page');

        $pending = Gallery::where('user_id', $user->id)
            ->where('status', GalleryEnum::STATUS_PENDING)
            ->orderBy('updated_at', 'DESC')
            ->paginate($perPage, ['*'], 'pending_page');


        $private = Gallery::where('user_id', $user->id)
            ->where('status', GalleryEnum::STATUS_PRIVATE)
            ->orderBy('updated_at', 'DESC')
            ->paginate($perPage ,['*'], 'private_page');

        $all = $this->hydrateGalleries($all);
        $public = $this->hydrateGalleries($public);
        $private = $this->hydrateGalleries($private);
        $pending = $this->hydrateGalleries($pending);

        return [
            'all' => $all,
            'public' => $public,
            'private' => $private,
            'pending' => $pending,
        ];
    }

    public function listPending()
    {
        $galleries = Gallery::with('user')
            ->where('status', GalleryEnum::STATUS_PENDING)
            ->get();
        return $this->hydrateGalleries($galleries);
    }

    public function listPublic()
    {
        $galleries = Gallery::with('user')
            ->where('status', GalleryEnum::STATUS_PUBLIC)
            ->paginate(SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH));
        return $this->hydrateGalleries($galleries);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (is_null($user)) {
            throw new \Exception('User not authenticated');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        return Gallery::create([
            'user_id' => $user->id,
            'name' => $request->input('name'),
            'description' => $data['description'] ?? null,
            'status' => $request->input('status', GalleryEnum::STATUS_PRIVATE),
        ]);
    }

    public function update(Gallery $gallery, array $data): void
    {
        $gallery->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        if (is_null($user)) {
            throw new \Exception('User not authenticated');
        }

        $gallery = Gallery::find($request->input('gallery_id'));
        if (!$gallery) {
            throw new \Exception('Gallery not found');
        }

        if ($gallery->user_id != $user->id) {
            throw new \Exception('Gallery does not belong to user');
        }

        $gallery->name = $request->input('name');
        $gallery->description = $request->input('description');
        $gallery->save();

        return $gallery;
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        if (is_null($user)) {
            throw new \Exception('User not authenticated');
        }

        $gallery = Gallery::find($request->input('gallery_id'));
        if (!$gallery) {
            throw new \Exception('Gallery not found');
        }

        if ($gallery->user_id != $user->id) {
            throw new \Exception('Gallery does not belong to user');
        }

        $gallery->delete();

        return $gallery;
    }

    public function addObjects(int $galleryId, array $objectIds): bool
    {
        $user = Auth::user();
        if (is_null($user)) {
            throw new \Exception('User not authenticated');
        }

        $gallery = Gallery::find($galleryId);
        if (!$gallery) {
            throw new \Exception('Gallery not found');
        }

        if ($gallery->user_id != $user->id) {
            throw new \Exception('Gallery does not belong to user');
        }

        $objects = CulturalObject::on('secondary')
            ->whereIn('id', $objectIds)
            ->pluck('id')
            ->toArray();

        if (!$objects) {
            throw new \Exception('Cultural objects not found');
        }

        foreach ($objects as $objectId) {
            GalleryCulturalObject::firstOrCreate([
                'gallery_id' => $gallery->id,
                'cultural_object_id' => $objectId,
            ]);
        }

        return true;
    }

    public function removeObjects(int $galleryId, array $objectIds): bool
    {

        $user = Auth::user();
        if (is_null($user)) {
            throw new \Exception('User not authenticated');
        }

        $gallery = Gallery::find($galleryId);

        if (!$gallery) {
            throw new \Exception('Gallery not found');
        }

        if ($gallery->user_id != $user->id) {
            throw new \Exception('Gallery does not belong to user');
        }

        $existingObjects = CulturalObject::on('secondary')
            ->whereIn('id', $objectIds)
            ->pluck('id')
            ->toArray();

        $missing = array_diff($objectIds, $existingObjects);

        if (!empty($missing)) {
            throw new \Exception('Cultural objects not found');
        }

        $itemsToDelete = GalleryCulturalObject::where('gallery_id', $gallery->id)
            ->whereIn('cultural_object_id', $objectIds)
            ->get();

        foreach ($itemsToDelete as $item) {
            $item->delete();
        }

        return true;
    }

    public function getGalleryWithObjects(int $galleryId)
    {
        $user = Auth::user();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $gallery = Gallery::where('id', $galleryId)
            ->where('user_id', $user->id)
            ->first();

        if (!$gallery) {
            throw new \Exception('Gallery not found or does not belong to user');
        }

        $perPage = SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH);

        $allObjectIds = GalleryCulturalObject::where('gallery_id', $gallery->id)
            ->orderBy('created_at', 'asc')
            ->pluck('cultural_object_id');

        $totalObjects = $allObjectIds->count();


        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $offset = ($currentPage * $perPage) - $perPage;

        $paginatedIds = $allObjectIds->slice($offset, $perPage)->all();

        $objects = CulturalObject::on('secondary')
            ->with('provider')
            ->whereIn('id', $paginatedIds)
            ->get();

        $orderedObjects = collect($paginatedIds)->map(fn($id) => $objects->firstWhere('id', $id));

        $paginatedObjects = new LengthAwarePaginator(
            $orderedObjects,
            $totalObjects,
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        $gallery->setRelation('objects', $paginatedObjects);

        return $gallery;
    }

    public function getPublicGalleryWithObjects(int $galleryId)
    {
        $gallery = Gallery::with('user')
            ->where('id', $galleryId)
            ->where('status', GalleryEnum::STATUS_PUBLIC)
            ->first();

        if (!$gallery) {
            throw new \Exception('Gallery not found or not public');
        }

        $perPage = SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH);

        $allObjectIds = GalleryCulturalObject::where('gallery_id', $gallery->id)
            ->orderBy('created_at', 'asc')
            ->pluck('cultural_object_id');

        $totalObjects = $allObjectIds->count();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $offset = ($currentPage * $perPage) - $perPage;

        $paginatedIds = $allObjectIds->slice($offset, $perPage)->all();

        $objects = CulturalObject::on('secondary')
            ->with('provider')
            ->whereIn('id', $paginatedIds)
            ->get();

        $orderedObjects = collect($paginatedIds)->map(fn($id) => $objects->firstWhere('id', $id));

        $paginatedObjects = new LengthAwarePaginator(
            $orderedObjects,
            $totalObjects,
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        $gallery->setRelation('objects', $paginatedObjects);

        return $gallery;
    }

    public function getObjectGalleries(int $objectId)
    {
        $user = Auth::user();
        if (is_null($user)) {
            throw new \Exception('User not authenticated');
        }

        return Gallery::where('user_id', $user->id)
            ->whereIn('id', function ($q) use ($objectId) {
                $q->select('gallery_id')
                    ->from('gallery_cultural_object')
                    ->where('cultural_object_id', $objectId)
                    ->whereNull('deleted_at');
            })
            ->get();
    }

    public function toggleShare(int $galleryId, bool $enabled): string|false
    {
        $user = Auth::user();

        if (is_null($user)) {
            return false;
        }

        $gallery = Gallery::where('id', $galleryId)
            ->where('user_id', $user->id)
            ->first();

        if (!$gallery) {
            return false;
        }

        $oldStatus = $gallery->status;

        if ($enabled) {
            if ($oldStatus === GalleryEnum::STATUS_PRIVATE) {
                $objectsCount = $gallery->cultural_objects()->count();

                if ($objectsCount === 0) {
                    return false;
                }

                $gallery->status = GalleryEnum::STATUS_PENDING;
                $gallery->requested_at = now();

                Mail::to($user->email)->send(new GalleryPublishRequestedMail($gallery));
            } else {
                $gallery->status = GalleryEnum::STATUS_PRIVATE;
            }
        } else {
            $gallery->status = GalleryEnum::STATUS_PRIVATE;
        }

        if ($gallery->status !== $oldStatus) {
            $gallery->save();
        }

        return $gallery->status;
    }

    private function hydrateGalleries($galleries)
    {
        $collection = $galleries instanceof \Illuminate\Pagination\AbstractPaginator
            ? $galleries->getCollection()
            : $galleries;

        $galleryIds = $collection->pluck('id');

        $galleryObjectLinks = GalleryCulturalObject::whereIn('gallery_id', $galleryIds)
            ->orderBy('created_at', 'asc')
            ->get();

        $objectIdsByGallery = $galleryObjectLinks->groupBy('gallery_id')
            ->map(fn($group) => $group->pluck('cultural_object_id'));

        $allObjectIds = $galleryObjectLinks->pluck('cultural_object_id')->unique();

        $objects = CulturalObject::on('secondary')
            ->without('main_web_view_resource')
            ->whereIn('id', $allObjectIds)
            ->get()
            ->keyBy('id');

        foreach ($collection as $gallery) {
            $currentObjectIds = $objectIdsByGallery->get($gallery->id, collect());

            $orderedObjects = $currentObjectIds->map(fn($id) => $objects->get($id))
                ->filter();

            $gallery->objects_count = $currentObjectIds->count();
            $gallery->setRelation('objects', $orderedObjects);
        }

        if ($galleries instanceof \Illuminate\Pagination\AbstractPaginator) {
            return $galleries->setCollection($collection);
        }

        return $collection;
    }


    /**
     * @param Gallery $gallery
     * @return void
     */
    public function approve(Gallery $gallery)
    {
        /** @var User $user */
        $user = $gallery->user;

        $gallery->status = GalleryEnum::STATUS_PUBLIC;
        $gallery->published_at = now();
        $gallery->save();

        Mail::to($user->email)->send(new GalleryApprovedMail($gallery));
    }

    /**
     * @param Gallery $gallery
     * @param string $reason
     * @return void
     */
    public function setPrivate(Gallery $gallery, string $reason)
    {
        /** @var User $user */
        $user = $gallery->user;

        $previousStatus = $gallery->status;

        $gallery->update([
            'status' => GalleryEnum::STATUS_PRIVATE,
            'rejection_reason' => $reason,
        ]);

        if ($previousStatus === GalleryEnum::STATUS_PENDING) {
            $actionType = 'reject';
        } else {
            $actionType = 'unpublish';
        }

        Mail::to($user->email)->send(new GalleryStatusUpdateMail($gallery, $reason, $actionType));
    }

    public function latestPublicCollections(int $limit = 6): Collection
    {
        return Gallery::query()
            ->where('status', GalleryEnum::STATUS_PUBLIC)
            ->orderByDesc('created_at')
            ->take($limit)
            ->with(['cultural_objects.cultural_object.main_web_view_resource'])
            ->get()
            ->map(function ($gallery) {
                $thumb = null;
                $placeholderType = 'default';

                foreach ($gallery->cultural_objects as $pivot) {
                    $obj = $pivot->getRelation('cultural_object');
                    if (!$obj) continue;

                    if ($placeholderType === 'default' && $obj->main_web_view_resource) {
                        $placeholderType = $obj->main_web_view_resource->visualizationtype ?? 'default';
                    }

                    if (!empty($obj->thumbnail_url)) {
                        $thumb = $obj->thumbnail_url;
                        break;
                    }
                }

                $gallery->preview_thumbnail_url = $thumb;
                $gallery->preview_placeholder_type = $placeholderType;

                return $gallery;
            });
    }
}
