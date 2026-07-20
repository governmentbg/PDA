<?php

namespace App\View\Components;


use App\Enums\FeedbackCategoryEnum;
use App\Enums\SettingEnum;
use Illuminate\View\Component;

class FeedbackForm extends Component
{
    public array $limits;
    public array $categories;
    public ?string $recaptchaSiteKey;
    public string $id;
    public bool $modal;

    public function __construct(string $id = 'feedbackForm', bool $modal = false)
    {
        $this->id = $id;
        $this->modal = $modal;

        $this->limits = [
            'subject' => (int) SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_SUBJECT_MAX),
            'description' => (int) SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_DESCRIPTION_MAX),
            'email' => (int) SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_EMAIL_MAX),
            'name' => (int) SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_NAME_MAX),
        ];

        $this->categories = FeedbackCategoryEnum::values();
        $this->recaptchaSiteKey = SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_RECAPTCHA_SITE_KEY)
            ?? config('feedback.recaptcha.site');
    }

    public function render()
    {
        return view('components.feedback-form');
    }
}
