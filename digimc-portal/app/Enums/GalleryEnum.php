<?php


namespace App\Enums;

/**
 * Class GalleryEnum
 *
 * @package App\Enums
 */
class GalleryEnum
{
    const STATUS_PUBLIC = 'public';
    const STATUS_PRIVATE = 'private';

    const STATUS_PENDING = 'pending';

    public static function getReadableStatus($status = null)
    {
        $statuses = [
            self::STATUS_PRIVATE => 'Лична',
            self::STATUS_PUBLIC => 'Публична',
            self::STATUS_PENDING => 'В очакване'
        ];

        if(is_null($status))
        {
            return $statuses;
        }

        return $statuses[$status] ?? 'Неизвестен статус';
    }

}
