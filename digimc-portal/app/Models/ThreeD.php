<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreeD extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'three_d';


    public $casts = [
        'id' => 'integer',
        'publisher' => 'string',
        'cultural_object_id' => 'integer',
        'sub_type' => 'string',
        'design_house' => 'string',
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
