<?php

namespace App\Models;

use Laratrust\Models\Role as RoleModel;
use OwenIt\Auditing\Contracts\Auditable;

class Role extends RoleModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = "role";
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];
    public $timestamps = true;
    public $casts = [
        'name' => 'string',
        'display_name' => 'string',
        'description' => 'string',
    ];
    public const ADMINISTRATOR = 'administrator';
    public const USER = 'user';

}
