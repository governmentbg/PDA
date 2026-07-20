<?php

namespace App\Enums;

class ArticleTypeEnum
{
    const STORY = 1;
    const NEWS = 2;
    const ANNOUNCEMENT = 3;
    const PUBLIC_PROCUREMENT = 4;
    const OPEN_JOB_POSITION = 5;
    const REGULATORY_DOCUMENT = 6;
    const RESPONSE_UNDER_ZDOI = 7;

    /**
     * @return array<int,string>
     */
    public static function keys(): array
    {
        return [
            self::STORY => 'article.type.story',
            self::NEWS => 'article.type.news',
            self::ANNOUNCEMENT => 'article.type.announcement',
            self::PUBLIC_PROCUREMENT => 'article.type.public_procurement',
            self::OPEN_JOB_POSITION => 'article.type.open_job_position',
            self::REGULATORY_DOCUMENT => 'article.type.regulatory_document',
            self::RESPONSE_UNDER_ZDOI => 'article.type.response_under_zdoi',
        ];
    }

    /**
     * @throws \Exception
     */
    public static function label(int $id): string
    {
        $key = self::keys()[$id] ?? null;
        return $key ? __($key) : (string)$id;
    }

    /**
     * @throws \Exception
     */
    public static function options(): array
    {
        return collect(self::keys())->map(fn($k) => __($k))->all();
    }


}
