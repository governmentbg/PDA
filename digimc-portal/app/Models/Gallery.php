<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string|null $preview_thumbnail_url
 * @property string|null $preview_placeholder_type
 */
class Gallery extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'gallery';

    protected $fillable = [
        'name',
        'user_id',
        'description',
        'status',
        'requested_at',
        'published_at',
        'rejection_reason',
    ];

    protected $dates = [
        'requested_at',
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cultural_objects(): HasMany
    {
        return $this->hasMany(GalleryCulturalObject::class, 'gallery_id', 'id');
    }



}
