<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasWebViewThumbnail extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'has_web_view_thumbnail';


    public $casts = [
        'cultural_object_id' => 'integer',
        'web_resource_id' => 'integer',
    ];


    public $timestamps = false;
    protected $guarded = ['*'];

}
