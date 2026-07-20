<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PurchasedWebResource extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'purchased_web_resource';

    protected $fillable = [
        'user_id',
        'web_resource_id',
        'payment_id',
        'purchased_at',
    ];

    protected $dates = ['purchased_at'];

    public function webResource()
    {
        return $this->belongsTo(WebResource::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
