<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'image';


    public $casts = [
        'id' => 'integer',
        'creation_date' => 'string',
        'publisher' => 'string',
        'issuer_person' => 'string',
        'issuing_year' => 'integer',
        'cultural_object_id' => 'integer',
        'sub_type' => 'string',
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
