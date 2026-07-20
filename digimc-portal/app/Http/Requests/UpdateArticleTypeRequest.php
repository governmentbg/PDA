<?php

namespace App\Http\Requests;

use App\Models\ArticleType;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleTypeRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return ArticleType::$rules;
    }
}
