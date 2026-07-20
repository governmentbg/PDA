<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodicalPublication extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'periodical_publication';


    public $casts = [
        'id' => 'string',
        'periodical_publication_type' => 'string',
        'text_object_id' => 'integer',
    ];


    public $timestamps = false;
    protected $guarded = ['*'];

}
