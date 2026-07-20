<?php

namespace App\Models;

use App\Enums\SettingEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PaymentItem extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'payment_item';

    protected $fillable = [
        'payment_id',
        'web_resource_id',
        'price',
    ];
    protected $appends = ['price_bgn'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function webResource()
    {
        return $this->belongsTo(WebResource::class);
    }

    public function getPriceBgnAttribute()
    {
        return round($this->price * SettingEnum::getValueByKeyword(SettingEnum::EUR_TO_BGN), 2);
    }
}
