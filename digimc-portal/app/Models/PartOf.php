<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartOf extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'part_of';


    public $casts = [
        'cultural_object_child_id' => 'integer',
        'cultural_object_parent_id' => 'integer',
    ];


    public $timestamps = false;
    protected $guarded = ['*'];

}
