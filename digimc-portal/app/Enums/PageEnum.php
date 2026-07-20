<?php

namespace App\Enums;

/**
 * Class PageEnum
 *
 * @package App\Enums
 */
class PageEnum
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const FOR_US_BG = 1;
    const FOR_US_EN = 2;
    const USER_GUIDE_BG = 3;
    const USER_GUIDE_EN = 4;

    const FAQ_BG = 5;
    const FAQ_EN = 6;

    /**
     * Връща четим статус на български
     *
     * @param string $status
     * @return string
     */
    public static function getReadableStatus($status): string
    {
        $statuses = [
            self::STATUS_DRAFT => 'Чернова',
            self::STATUS_PUBLISHED => 'Публикувана',
        ];

        return $statuses[$status] ?? 'Неизвестен статус';
    }
}
