<?php

namespace App\Http\Requests;

use App\Enums\SettingEnum;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Article;

class CreateArticleRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user()->hasRole(Role::ADMINISTRATOR);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxSize = SettingEnum::getValueByKeyword(SettingEnum::ARTICLE_IMAGE_MAX_SIZE);

        return array_merge(Article::$rules, [
            'image' => "nullable|image|max:{$maxSize}",
        ]);
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'image.max' => __('validation.custom.image.max'),
            'image.uploaded' => __('validation.custom.image.uploaded'),
        ];
    }
}
