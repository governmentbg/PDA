<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class ArticleImage extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $table = "article_image";
    protected $fillable = [
        'article_id',
        'sort_weight',
        'filepath',
        'filename',
        'description',
    ];
    public $timestamps = true;
    public $casts = [
        'article_id' => 'integer',
        'sort_weight' => 'integer',
        'filepath' => 'string',
        'filename' => 'string',
        'description' => 'string',
    ];


    public static $rules = [
        'article_id' => 'required',
        'filepath' => 'required',
        'filename' => 'required',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }
}
