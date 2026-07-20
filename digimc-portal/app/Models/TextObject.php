<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextObject extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'text_object';


    public $casts = [
        'id' => 'integer',
        'year_of_publication' => 'integer',
        'date_of_publication' => 'string',
        'translator' => 'string',
        'writer' => 'string',
        'sponsor' => 'string',
        'issuer_person' => 'string',
        'publisher' => 'string',
        'first_аuthor' => 'string',
        'cultural_object_id' => 'integer',
        'sub_type' => 'string',
        'issuing_institution' => 'string',
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
