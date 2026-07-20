<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'video';


    public $casts = [
        'id' => 'integer',
        'years_issued' => 'integer',
        'film_duration' => 'string',
        'actor' => 'string',
        'filmmaker' => 'string',
        'scenario_writer' => 'string',
        'cameraman' => 'string',
        'producer' => 'string',
        'composer' => 'string',
        'mount' => 'string',
        'production_director' => 'string',
        'editor' => 'string',
        'other_related_persons' => 'string',
        'premiere' => 'string',
        'cultural_object_id' => 'integer',
        'sub_type' => 'string',
        'interviewer' => 'string',
        'interviewee' => 'string',
    ];


    public $timestamps = false;
    protected $guarded = ['*'];

    public function getSubTypeAttribute($value): string
    {
        if (!$value) {
            return '';
        }

        $codes = array_map('trim', explode(',', $value));

        $labels = CodeValue::labelsForCodes($codes);

        return implode(', ', $labels);
    }

}
