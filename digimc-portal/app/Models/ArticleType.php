<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class ArticleType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasFactory;

    protected $table = "article_type";
    protected $fillable = [
        'name',
    ];
    public $timestamps = true;
    public $casts = [
        'name' => 'string',
    ];
    public static $rules = [
        'name' => 'required',
    ];



}
