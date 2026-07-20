<?php

namespace App\Models;

use Laratrust\Models\Permission as PermissionModel;
use OwenIt\Auditing\Contracts\Auditable;

class Permission extends PermissionModel implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    public $guarded = [];
}
