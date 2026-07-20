<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return User::$profileUpdateRules;
    }

    public function messages(): array
    {
        return [
            'first_name.required' => __('validation.required'),
            'last_name.required' => __('validation.required'),
            'profile_image_path.mimes' => __('validation.mimes', ['attribute' => __('profile.fields.profile_photo'), 'values' => 'jpg, jpeg, png, webp']),
            'profile_image_path.max' => __('validation.max.file', ['attribute' => __('profile.fields.profile_photo'), 'max' => 2048]),
            'current_password.required' => __('validation.required'),
            'current_password.current_password' => __('validation.current_password'),
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => __('profile.fields.first_name'),
            'last_name' => __('profile.fields.last_name'),
            'profile_image_path' => __('profile.fields.profile_photo'),
            'current_password' => __('profile.fields.current_password'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'wants_notifications' => $this->normalizeBool($this->input('wants_notifications')),
            'subscribed_news' => $this->normalizeBool($this->input('subscribed_news')),
            'subscribed_weekly' => $this->normalizeBool($this->input('subscribed_weekly')),
        ]);
    }

    private function normalizeBool($v)
    {
        if (is_bool($v)) return $v;
        if ($v === 1 || $v === '1' || $v === 'on' || $v === 'true') return 1;
        if ($v === 0 || $v === '0' || $v === 'off' || $v === 'false') return 0;
        return $v;
    }
}
