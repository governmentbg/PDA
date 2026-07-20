<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'provider';

    public $casts = [
        'id' => 'integer',
        'identifier' => 'string',
        'type' => 'string',
        'phone_number' => 'string',
        'address' => 'string',
        'email' => 'string',
        'website' => 'string',
        'territory' => 'string',
        'contact_person' => 'string',
        'title' => 'string',
    ];

    public $timestamps = false;
    protected $guarded = ['*'];


    public function cultural_objects()
    {
        return $this->hasMany(CulturalObject::class, 'cultural_object_provided_by', 'id');
    }
}
