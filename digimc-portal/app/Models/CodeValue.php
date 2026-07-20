<?php

namespace App\Models;

use App\Abstracts\Models\ReadonlyModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeValue extends ReadonlyModel
{
    use HasFactory;

    protected $connection = 'secondary';

    protected $table = 'code_value';


    public $casts = [
        'id' => 'integer',
        'code' => 'string',
        'value_bg' => 'string',
        'value_en' => 'string',
    ];


    public $timestamps = false;
    protected $guarded = ['*'];

    public static function labelsForCodes(array $codes): array
    {
        if (empty($codes)) {
            return [];
        }

        $locale = app()->getLocale();
        $column = $locale === \App\Enums\SettingEnum::LOCALE_BG ? 'value_bg' : 'value_en';

        return static::query()
            ->whereIn('code', $codes)
            ->pluck($column, 'code')
            ->toArray();
    }
}
