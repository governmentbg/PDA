<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class RoleUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'role_user';

    protected $fillable = [
        'role_id',
        'user_id',
        'user_type',
    ];

    protected $guarded = [];

    public $timestamps = false;
}
