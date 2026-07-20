<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends ReadonlyModel
{
    use HasFactory;

    protected $connection = 'secondary';

    protected $table = 'audio';


    public $casts = [
        'id' => 'integer',
        'performer' => 'string',
        'producer' => 'string',
        'duration' => 'string',
        'recording_team' => 'string',
        'audio_original_title' => 'string',
        'composer' => 'string',
        'author_of_arrangement' => 'string',
        'text_author' => 'string',
        'editing_producer_name' => 'string',
        'date_recorded' => 'datetime',
        'broadcasting_date' => 'datetime',
        'colutral_object_id' => 'integer',
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
