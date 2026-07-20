<?php

namespace App\Models;

use App\Enums\SettingEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\PaymentStatusEnum;
use OwenIt\Auditing\Contracts\Auditable;

class Payment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory, SoftDeletes;

    protected $table = 'payment';

    protected $fillable = [
        'user_id',
        'payment_code',
        'external_transaction_id',
        'status',
        'total_amount',
        'expires_at',
        'paid_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'paid_at'    => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $dates = ['expires_at', 'paid_at'];
    protected $appends = ['amount_bgn'];

    public function items(): HasMany
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function purchasedResources()
    {
        return $this->hasMany(PurchasedWebResource::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatusEnum::PAID;
    }

    public function getAmountBgnAttribute()
    {
        return round($this->total_amount * SettingEnum::getValueByKeyword(SettingEnum::EUR_TO_BGN), 2);
    }
}
