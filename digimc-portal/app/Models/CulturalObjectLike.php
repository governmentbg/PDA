<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class CulturalObjectLike extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $table = "cultural_object_like";
    protected $fillable = [
        'cultural_object_id',
        'user_id',
    ];
    public $timestamps = true;
    public $casts = [
        'cultural_object_id' => 'integer',
        'user_id' => 'integer',
    ];
    public static $rules = [
        'cultural_object_id' => 'required',
        'user_id' => 'required',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
