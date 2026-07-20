<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property-read \App\Models\CulturalObject|null $cultural_object
 */
class GalleryCulturalObject extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'gallery_cultural_object';

    public $timestamps = true;
    protected $fillable = ['gallery_id', 'cultural_object_id'];

    public function cultural_object(): BelongsTo
    {
        return $this->belongsTo(CulturalObject::class, 'cultural_object_id');
    }
}
