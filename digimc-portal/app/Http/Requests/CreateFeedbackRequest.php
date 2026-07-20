<?php

namespace App\Http\Requests;

use App\Enums\FeedbackCategoryEnum;
use App\Enums\SettingEnum;
use App\Rules\RecaptchaRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $limits = [
            'subject' => (int) SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_SUBJECT_MAX),
            'description' => (int) SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_DESCRIPTION_MAX),
            'email' => (int) SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_EMAIL_MAX),
            'name' => (int) SettingEnum::getValueByKeyword(SettingEnum::FEEDBACK_NAME_MAX),
        ];

        $categories = FeedbackCategoryEnum::values();

        return [
            'subject' => ['required','string','max:'. $limits['subject']],
            'category' => ['required', Rule::in($categories)],
            'description' => ['required','string','max:'. $limits['description']],
            'contact_email' => ['required','email','max:'. $limits['email']],
            'name' => ['required','string','max:'. $limits['name']],
            'g-recaptcha-response' => ['required','string', new RecaptchaRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('feedback.validation.required'),
            'email' => __('feedback.validation.email'),
            'string' => __('feedback.validation.string'),
            'max.string' => __('feedback.validation.max.string'),
            'in' => __('feedback.validation.in'),
            'g-recaptcha-response.required' => __('feedback.captcha_failed'),
        ];
    }

}
