<?php

namespace App\Enums;

class FeedbackCategoryEnum
{
    public const PROBLEM    = 'problem';
    public const SUGGESTION = 'suggestion';
    public const PRAISE     = 'praise';
    public const QUESTION   = 'question';

    /**
     * Return all constants as an array of values.
     */
    public static function values(): array
    {
        return [
            self::PROBLEM,
            self::SUGGESTION,
            self::PRAISE,
            self::QUESTION,
        ];
    }

    public static function all(): array
    {
        return self::values();
    }
}
