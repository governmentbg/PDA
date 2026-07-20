<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\PasswordResetMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Mail;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements LaratrustUser, Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use \OwenIt\Auditing\Auditable;
    use HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'profile_image_path',
        'wants_notifications',
        'subscribed_news',
        'subscribed_weekly',
        'activation_token',
        'activation_token_expires_at',
        'email_verified_at',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'activation_token_expires_at' => 'datetime',
            'wants_notifications' => 'boolean',
            'subscribed_news' => 'boolean',
            'subscribed_weekly' => 'boolean',
            'password' => 'hashed',
            'locked_until' => 'datetime',
        ];
    }


    public function likes() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CulturalObjectLike::class, 'user_id', 'id');
    }

    public static array $profileUpdateRules = [
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],

        'profile_image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

        'wants_notifications' => ['nullable', 'boolean'],
        'subscribed_news' => ['nullable', 'boolean'],
        'subscribed_weekly' => ['nullable', 'boolean'],

        // password confirmation
        'current_password' => ['required', 'current_password'],
    ];

    /**
     * Sends password reset mail
     *
     */
    public function sendPasswordResetNotification($token)
    {
        $url = url(route('auth.password.reset', [
            'token' => $token,
            'email' => $this->email,
        ], false));

        Mail::to($this->email)->send(new PasswordResetMail($this, $url));
    }
}
