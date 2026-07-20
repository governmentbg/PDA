<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use App\Enums\CodelistEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class CulturalObject extends ReadonlyModel
{
    use HasFactory;

    protected $connection = 'secondary';

    protected $table = 'cultural_object';


    public $casts = [
        'identifier' => 'string',
        'type' => 'string',
        'title' => 'string',
        'original_title' => 'string',
        'other_title' => 'string',
        'artist' => 'string',
        'description' => 'string',
        'cultural_object_provided_by' => 'integer',
        'creation_date' => 'string',
        'current_location' => 'string',
        'keywords' => 'string',
        'theme' => 'string',
        'subject_heading' => 'string',
        'geographic_heading' => 'string',
        'temporal_heading' => 'string',
        'language_code' => 'string',
        'physical_dimensions' => 'string',
        'medium' => 'string',
        'previous_owner' => 'string',
        'acquisition' => 'string',
        'original_media' => 'string',
        'rights_holder' => 'string',
        'rights' => 'string',
        'contentdescription' => 'string',
        'id' => 'integer',
        'amount' => 'float',
        'currency' => 'string',
        'thumbnail_url' => 'string',
        'extended_view_url' => 'string',
    ];


    public $timestamps = false;
    protected $guarded = ['*'];
    protected $with = ['main_web_view_resource'];

    /**
     * Get the provider that owns the cultural object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Provider::class, 'cultural_object_provided_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function has_web_view_resource() :HasManyThrough
    {
        return $this->hasManyThrough(WebResource::class, HasWebView::class , 'cultural_object_id', 'id' ,'id', 'web_resource_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function main_web_view_resource() :HasOneThrough
    {
        return $this->hasOneThrough(WebResource::class, HasWebView::class , 'cultural_object_id', 'id' ,'id', 'web_resource_id')->where('type', CodelistEnum::MAIN_WEB_RESOURCE);
    }

    /**
     * cultural object type: images
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'cultural_object_id', 'id');
    }

    /**
     * cultural object type: text
     * @return HasMany
     */
    public function text_objects(): HasMany
    {
        return $this->hasMany(TextObject::class, 'cultural_object_id', 'id');
    }


    /**
     * cultural object type: video
     * @return HasMany
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, 'cultural_object_id', 'id');
    }

    /**
     * cultural object type: audio
     * @return HasMany
     */
    public function audios(): HasMany
    {
        return $this->hasMany(Audio::class, 'cultural_object_id', 'id');
    }

    /**
     * cultural object type: 3d
     * @return HasMany
     */
    public function three_ds(): HasMany
    {
        return $this->hasMany(ThreeD::class, 'cultural_object_id', 'id');
    }

    public function parent_objects()
    {
        return $this->belongsToMany(
            CulturalObject::class,
            'part_of',
            'cultural_object_child_id',
            'cultural_object_parent_id'
        );
    }

    public function components()
    {
        return $this->belongsToMany(
            CulturalObject::class,
            'has_component',
            'cultural_object_id',
            'cultural_object_child_id'
        );
    }

    public function getThumbnailUrlAttribute($value)
    {
        if(empty($value))
        {
            return null;
        }

        $base = config('services.cdn_base_url');
        $path = ltrim($value, '/');

        return $base.$path;
    }

    public function getExtendedViewUrlAttribute($value)
    {
        if(empty($value))
        {
            return null;
        }

        $base = config('services.cdn_base_url');
        $path = ltrim($value, '/');

        return $base.$path;
    }
}
