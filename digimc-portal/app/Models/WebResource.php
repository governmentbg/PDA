<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use App\Enums\CodelistEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property bool $paid_content
 * @property int $id
 */
class WebResource extends ReadonlyModel
{
    use HasFactory;
    protected $connection = 'secondary';

    protected $table = 'web_resource';

    public $casts = [
        'id' => 'integer',
        'identifier' => 'string',
        'type' => 'string',
        'creator' => 'string',
        'description' => 'string',
        'format' => 'string',
        'rights_holder' => 'string',
        'resource_type' => 'string',
        'conforms_to' => 'string',
        'created_at' => 'string',
        'extent' => 'string',
        'issued' => 'string',
        'web_resource_address' => 'string',
        'rights' => 'string',
        'sensitive_content' => 'string',
        'content_warning' => 'string',
        'warning_text' => 'string',
        'visualizationtype' => 'string',
        'paid_content' => 'string',
        'price' => 'float',
        'trailer_address' => 'string',
        'web_resource_address_download' => 'string',
        'mimetype_thumbnail' => 'string',
        'mimetype_trailer' => 'string',
        'mimetype_download' => 'string',
        'source' => 'string',
        'title' => 'string',
    ];


    public $timestamps = false;
    protected $guarded = ['*'];

    public function culturalObjects()
    {
        return $this->hasManyThrough(
            \App\Models\CulturalObject::class,
            \App\Models\HasWebView::class,
            'web_resource_id',
            'id',
            'id',
            'cultural_object_id'
        );
    }

    public function isPaid(): bool
    {
        $value = $this->paid_content;

        if (!$value) {
            return false;
        }

        $value = mb_strtoupper(trim((string) $value));

        //todo paid_content comes from code list – value may be MCD code, YES/NO, 1/0 etc.
        return in_array($value, [
            'YES', 'DA', 'ДА', 'TRUE', '1'
        ], true);
    }

    public function isPayableType(): bool
    {
        return in_array($this->visualizationtype, [
            \App\Enums\CulturalObjectEnum::VIDEO,
            \App\Enums\CulturalObjectEnum::AUDIO,]);
    }

    public function purchases()
    {
        return $this->hasMany(PurchasedWebResource::class, 'web_resource_id', 'id');
    }

    public function isPurchasedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return PurchasedWebResource::where('web_resource_id', $this->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function getWebResourceAddressAttribute($value)
    {
        $base = config('services.cdn_base_url');
        $path = ltrim($value, '/');

        return $base.$path;
    }

    public function isSensitive(): bool
    {
        return $this->sensitive_content === CodelistEnum::SENSITIVE_CONTENT;
    }

    public function sensitiveLabels(): array
    {
        if (!$this->content_warning) {
            return [];
        }

        $codes = array_map('trim', explode(',', $this->content_warning));

        return CodeValue::labelsForCodes($codes);
    }

    public function getRightsAttribute($value): string
    {
        if (!$value) {
            return '';
        }

        $codes = array_map('trim', explode(',', $value));

        $labels = CodeValue::labelsForCodes($codes);

        return implode(', ', $labels);
    }
}
