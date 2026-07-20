<?php

namespace App\Http\Controllers;

use App\Enums\ArticleEnum;
use App\Enums\SettingEnum;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        try {
            $data = [
                'articles' => Article::with(['image', 'type'])->where('status', ArticleEnum::STATUS_PUBLISHED)->orderBy(
                    'published_at',
                    'DESC'
                )->paginate(SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH)),
            ];

            return view('pages.article.index', $data);
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function view($articleId)
    {
        try {

            $data = [
                'article' => Article::with(['image', 'type'])->where(['id' => $articleId, 'status' => ArticleEnum::STATUS_PUBLISHED])->first(),
            ];
            return view('pages.article.article', $data);
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

}
