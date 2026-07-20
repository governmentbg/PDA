<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class CartItem extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $table = 'cart_item';

    protected $fillable = [
        'cart_id',
        'web_resource_id',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function webResource()
    {
        return $this->belongsTo(WebResource::class);
    }
}
