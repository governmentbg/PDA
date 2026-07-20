<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodicalPublicationNumber extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'periodical_publication_number';


    public $casts = [
        'id' => 'integer',
        'periodical_publication_issue_number' => 'string',
        'periodical_publication_issue_year' => 'string',
        'periodical_publication_issue_month' => 'string',
        'periodical_publication_issue_day' => 'string',
        'text_object_id' => 'integer',
        'periodical_publication_id' => 'integer',
    ];


    public $timestamps = false;
    protected $guarded = ['*'];

}
