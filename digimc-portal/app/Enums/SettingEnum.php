<?php


namespace App\Enums;


use App\Enums;
use App\Models\Setting;

/**
 * Class SettingEnum
 *
 * @package App\Enums
 */
class SettingEnum
{
    const SETTINGS_EXPIRED_MESSAGE = 'session_expired_message';

    const SETTINGS_PAGINATION_LENGTH = 'settings_pagination_length';

    const FEEDBACK_MESSAGE_SUCCESS = 'feedback_success_message';
    const FEEDBACK_MESSAGE_GENERIC_ERROR = 'feedback_generic_error_message';
    const FEEDBACK_MESSAGE_CAPTCHA_FAILED = 'feedback_captcha_failed_message';

    const FEEDBACK_SUBJECT_MAX = 'feedback_subject_max';
    const FEEDBACK_DESCRIPTION_MAX = 'feedback_description_max';
    const FEEDBACK_NAME_MAX = 'feedback_name_max';
    const FEEDBACK_EMAIL_MAX = 'feedback_email_max';

    const FEEDBACK_MAIL_ENABLED = 'true';
    const FEEDBACK_FROM_CONTACT_EMAIL = 'from_contact_email';
    const FEEDBACK_TO_CONTACT_EMAIL = 'to_contact_email';


    const FEEDBACK_RECAPTCHA_VERSION = 'feedback_recaptcha_version';
    const FEEDBACK_RECAPTCHA_SITE_KEY = 'feedback_recaptcha_site_key';
    const FEEDBACK_RECAPTCHA_SECRET = 'feedback_recaptcha_secret';

    const FEEDBACK_UI_BUBBLE_TEXT = 'feedback_ui_bubble_text';
    const FEEDBACK_UI_POSITION = 'feedback_ui_position';
    const RECAPTCHA_SITE_KEY = 'recaptcha_site_key';
    const RECAPTCHA_SECRET = 'recaptcha_secret';
    const RECAPTCHA_ENABLED = 'recaptcha_enabled';
    const SESSION_LIFETIME = 'session_lifetime';

    const PAYMENT_EXPIRES_AT = 'payment_expires_at';
    const LOCALE_BG = 'bg';
    const LOCALE_EN = 'en';

    const EUR_TO_BGN = 'eur_to_bgn';

    const ARTICLE_IMAGE_MAX_SIZE = 'article_image_max_size';

    const PAYMENT_SYNC_DAYS = 'payment_sync_days';

    const PAYMENT_REASON = 'payment_reason';

    const SEARCH_INFORMATION_TEXT_BG = 'search_info_text_bg';

    const SEARCH_INFORMATION_TEXT_EN = 'search_info_text_en';


    public static function getValueByKeyword($keyword)
    {
        $settingFromDb = Setting::select('value')->where('keyword', $keyword)->first();
        if(is_null($settingFromDb))
        {
            $default = self::getKeywordDefaultValue($keyword);

            if(is_array($default))
            {
                throw new \Exception('Възникна проблем при извличане на информация. Моля, опитайте по-късно');
            }

            return $default;
        } else {
            return $settingFromDb->value;
        }
    }

    public static function getKeywordDefaultValue($keyword = null)
    {
        //@todo maybe add a description for them - SETTINGS_EXPIRED_MESSAGE = 'Съобщение при изтичане на сесията на потребител'
        $all = [
            self::SETTINGS_EXPIRED_MESSAGE => 'Вашата сесия е изтекла поради продължителна неактивност. Моля, влезте отново.',
            self::SETTINGS_PAGINATION_LENGTH => 10,

            self::FEEDBACK_MESSAGE_SUCCESS => 'Thank you, your message was sent successfully.',
            self::FEEDBACK_MESSAGE_GENERIC_ERROR => 'Something went wrong. Please try again.',
            self::FEEDBACK_MESSAGE_CAPTCHA_FAILED => 'Captcha validation failed.',

            self::FEEDBACK_SUBJECT_MAX => 150,
            self::FEEDBACK_DESCRIPTION_MAX => 5000,
            self::FEEDBACK_NAME_MAX => 120,
            self::FEEDBACK_EMAIL_MAX => 255,

            self::FEEDBACK_MAIL_ENABLED => 'true',
            self::FEEDBACK_FROM_CONTACT_EMAIL => config('feedback.mail.fallback_from', 'sample@email.com'),
            self::FEEDBACK_TO_CONTACT_EMAIL => config('feedback.mail.fallback_to', 'sample@email.com'),

            self::FEEDBACK_RECAPTCHA_VERSION => config('feedback.recaptcha.version', 'v2_checkbox'),
            self::FEEDBACK_RECAPTCHA_SITE_KEY => config('feedback.recaptcha.site', 'insert site key here'),
            self::FEEDBACK_RECAPTCHA_SECRET => config('feedback.recaptcha.secret', 'insert secret key here'),

            self::RECAPTCHA_SITE_KEY => config('services.recaptcha.site_key', 'insert site key here'),
            self::RECAPTCHA_SECRET => config('services.recaptcha.secret_key', 'insert secret key here'),
            self::RECAPTCHA_ENABLED => config('services.recaptcha.enabled', true),


            self::FEEDBACK_UI_BUBBLE_TEXT => 'Feedback',
            self::FEEDBACK_UI_POSITION => 'bottom-right',

            self::SESSION_LIFETIME => 30,

            self::EUR_TO_BGN => 1.95583,
            self::PAYMENT_EXPIRES_AT => 3,
            self::ARTICLE_IMAGE_MAX_SIZE => 5120,

            self::PAYMENT_SYNC_DAYS => 3,
            self::PAYMENT_REASON => 'Такса за услуга',

            self::SEARCH_INFORMATION_TEXT_BG => "Информация от Държавна агенция \"Архиви\" можете да откриете на официалната страница на ДАА: https://www.archives.government.bg/ ",
            self::SEARCH_INFORMATION_TEXT_EN => "Information from the State Agency \"Archives\" can be found on the official SAA website: https://www.archives.government.bg/ ",
        ];


        return (!is_null($keyword) && array_key_exists($keyword, $all))? $all[$keyword] : $all;
    }

    public static function getHumanReadableName($keyword)
    {
        $names = [
            self::SETTINGS_EXPIRED_MESSAGE => 'Съобщение за Изтекла Сесия',
            self::SETTINGS_PAGINATION_LENGTH => 'Елементи на Страница (Пагинация)',
            self::SESSION_LIFETIME => 'Продължителност на Сесията (мин.)',
            self::FEEDBACK_MESSAGE_SUCCESS => 'Съобщение за Успешно Изпращане (Feedback)',
            self::FEEDBACK_MESSAGE_GENERIC_ERROR => 'Общо Съобщение за Грешка (Feedback)',
            self::FEEDBACK_MESSAGE_CAPTCHA_FAILED => 'Съобщение за Неуспешна reCAPTCHA Проверка',
            self::FEEDBACK_SUBJECT_MAX => 'Макс. Дължина на Тема (Feedback)',
            self::FEEDBACK_DESCRIPTION_MAX => 'Макс. Дължина на Описание (Feedback)',
            self::FEEDBACK_NAME_MAX => 'Макс. Дължина на Име (Feedback)',
            self::FEEDBACK_EMAIL_MAX => 'Макс. Дължина на Имейл Адрес (Feedback)',
            self::FEEDBACK_MAIL_ENABLED => 'Имейл Изпращане Активно (Feedback)',
            self::FEEDBACK_FROM_CONTACT_EMAIL => 'Имейл Адрес \'Изпратено От\'',
            self::FEEDBACK_TO_CONTACT_EMAIL => 'Имейл Адрес \'Изпратено До\'',
            self::FEEDBACK_RECAPTCHA_VERSION => 'reCAPTCHA Версия (Feedback)',
            self::FEEDBACK_RECAPTCHA_SITE_KEY => 'reCAPTCHA Ключ за Сайт (Feedback)',
            self::FEEDBACK_RECAPTCHA_SECRET => 'reCAPTCHA Секретен Ключ (Feedback)',
            self::RECAPTCHA_SITE_KEY => 'reCAPTCHA Ключ за Сайт (Общ/Вход)',
            self::RECAPTCHA_SECRET => 'reCAPTCHA Секретен Ключ (Общ/Вход)',
            self::RECAPTCHA_ENABLED => 'reCAPTCHA Активно (Общо)',
            self::FEEDBACK_UI_BUBBLE_TEXT => 'Текст на Бутона (Feedback UI)',
            self::FEEDBACK_UI_POSITION => 'Позиция на Бутона (Feedback UI)',
            self::LOCALE_BG => 'Език: Български',
            self::LOCALE_EN => 'Език: Английски',
            self::EUR_TO_BGN => 'Валутен курс (EUR към BGN)',
            self::PAYMENT_EXPIRES_AT => 'Валидност на кода (дни)',
            self::PAYMENT_SYNC_DAYS => 'Период за проверка на плащания (в дни)',
            self::ARTICLE_IMAGE_MAX_SIZE => 'Максимален размер на снимка за новини (KB)',
            self::PAYMENT_REASON => 'Причина за плащане',
            self::SEARCH_INFORMATION_TEXT_BG => 'Информационен текст при търсене (BG)',
            self::SEARCH_INFORMATION_TEXT_EN => 'Информационен текст при търсене (EN)',
        ];

        return $names[$keyword] ?? $keyword;
    }

}
