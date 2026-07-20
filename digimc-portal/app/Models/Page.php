<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Page extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, SoftDeletes;

    protected $table = "page";

    protected $fillable = [
        'title',
        'sef_title',
        'content',
        'status',
    ];

    public $timestamps = true;

    protected $casts = [
        'title' => 'string',
        'sef_title' => 'string',
        'content' => 'string',
        'status' => 'string',
    ];

    public static $rules = [
        'title' => 'required|max:250',
        'sef_title' => 'required',
        'content' => 'min:10',
    ];

}
