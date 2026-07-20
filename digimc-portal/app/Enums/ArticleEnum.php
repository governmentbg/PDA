<?php


namespace App\Enums;


use App\Enums;
use App\Models\Setting;

/**
 * Class ArticleEnum
 *
 * @package App\Enums
 */
class ArticleEnum
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    public static function getReadableStatus($status)
    {
        $statuses = [
            self::STATUS_DRAFT => 'Чернова',
            self::STATUS_PUBLISHED => 'Публикувана',
        ];

        return $statuses[$status] ?? 'Неизвестен статус';
    }

}
