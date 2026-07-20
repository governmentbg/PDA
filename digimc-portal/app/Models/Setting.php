<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class Setting extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasFactory;

    public $table = 'setting';

    public $fillable = [
        'keyword',
        'value',
    ];

    protected $casts = [

    ];

    public static array $rules = [
        'keyword' => 'required',
        'value' => 'required',
    ];


}
