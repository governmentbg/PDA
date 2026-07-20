<?php

namespace App\Enums;

class CulturalObjectEnum
{
    const IMAGE = 'image';
    const VIDEO = 'video';
    const MOVIE = 'movie';
    const TIFF = 'tiff';
    const PDF = 'pdf';
    const TEXT = 'text';
    const AUDIO = 'audio';
    const THREE_D = '3d';
    const MISC = 'misc';

    const TYPES = [
        self::IMAGE,
        self::VIDEO,
        self::MOVIE,
        self::TIFF,
        self::TEXT,
        self::AUDIO,
        self::THREE_D,
        self::MISC,
    ];
    const RELATIONS_LOAD = [
        self::IMAGE => ['images', 'has_web_view_resource'],
        self::TEXT => ['text_objects', 'has_web_view_resource'],
        self::MOVIE => ['videos', 'has_web_view_resource'],
        self::AUDIO => ['audios', 'has_web_view_resource'],
        self::THREE_D => ['three_ds', 'has_web_view_resource'],
    ];

    const DOWNLOADABLE = [

    ];

    public static function loadRelations($type = null)
    {
        $relations = self::RELATIONS_LOAD;

        return (!is_null($type) && array_key_exists($type, $relations)) ? $relations[$type] : [];
    }

    public static function getReadableVisualisation(?string $type = null)
    {
        $labels = [
            self::PDF     => __('cultural_object.web_resource_type.pdf'),
            self::AUDIO    => __('cultural_object.web_resource_type.audio'),
            self::VIDEO    => __('cultural_object.web_resource_type.video'),
            self::IMAGE    => __('cultural_object.web_resource_type.image'),
            self::TIFF     => __('cultural_object.web_resource_type.image'),
            self::THREE_D  => __('cultural_object.web_resource_type.3d'),
            self::MISC     => __('cultural_object.web_resource_type.misc'),
        ];

        if (is_null($type)) {
            return $labels;
        }

        return $labels[$type] ?? null;
    }

}
