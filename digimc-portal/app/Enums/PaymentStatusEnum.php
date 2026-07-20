<?php

namespace App\Enums;

class PaymentStatusEnum
{
    const PENDING = 'pending';
    const AUTHORIZED = 'authorized';
    const ORDERED = 'ordered';
    const PAID = 'paid';
    const EXPIRED = 'expired';
    const CANCELED = 'canceled';
    const SUSPENDED = 'suspended';

    const ALL = [
        self::PENDING,
        self::AUTHORIZED,
        self::ORDERED,
        self::PAID,
        self::EXPIRED,
        self::CANCELED,
        self::SUSPENDED,
    ];

    public static function getReadableStatus($status = null)
    {
        $statuses = [
            self::PENDING    => 'В изчакване',
            self::AUTHORIZED => 'Оторизирано',
            self::ORDERED    => 'Поръчано',
            self::PAID       => 'Платено',
            self::EXPIRED    => 'Изтекло',
            self::CANCELED   => 'Отказано',
            self::SUSPENDED  => 'Спряно',
        ];

        if (is_null($status)) {
            return $statuses;
        }

        return $statuses[$status] ?? 'Неизвестен статус';
    }


    public static function finished(): array
    {
        return [
            self::PAID,
            self::EXPIRED,
            self::CANCELED,
            self::SUSPENDED,
        ];
    }

    public static function active(): array
    {
        return [
            self::PENDING,
            self::AUTHORIZED,
            self::ORDERED,
        ];
    }
}
