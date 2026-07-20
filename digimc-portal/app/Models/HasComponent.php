<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasComponent extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'has_component';


    public $casts = [
        'cultural_object_id' => 'integer',
        'cultural_object_child_id' => 'integer',
    ];


    public $timestamps = false;
    protected $guarded = ['*'];

}
