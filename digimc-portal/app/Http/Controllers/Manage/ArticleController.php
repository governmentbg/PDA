<?php

namespace App\Http\Controllers\Manage;

use App\DataTables\ArticleDataTable;
use App\Enums\ArticleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateArticleRequest;
use App\Http\Requests\DeleteArticleRequest;
use App\Http\Requests\TogglePublishArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\ArticleType;
use App\Services\ArticleService;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of the Article.
     *
     * @param ArticleDataTable $articleDataTable
     * @return Response
     */
    public function index(ArticleDataTable $articleDataTable)
    {

        return $articleDataTable->render('articles.index');
    }

    /**
     * Show the form for creating a new Article.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View|object
     */
    public function create()
    {
        $data = [
            'article_types' => ArticleType::get(),
        ];

        return view('articles.create', $data);
    }

    /**
     * Store a newly created Article in storage.
     *
     * @param CreateArticleRequest $request
     *
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|object
     */
    public function store(CreateArticleRequest $request)
    {
        try {

            $articleService = new ArticleService();
            $articleService->store($request);

            Flash::success('Новината е запазена успешно.');

            return redirect(route('manage.article.index'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified Article.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View|object
     */
    public function edit($id)
    {
        /** @var Article|null $article */
        $article = Article::with(['image','type'])->find($id);

        if ($article === null) {
            Flash::error('Новината не е намерена');

            return redirect(route('manage.article.index'));
        }

        $data = [
            'article' => $article,
            'article_types' => ArticleType::get(),
        ];

        return view('articles.edit', $data);
    }

    /**
     * Update the specified Article in storage.
     *
     * @param int $id
     * @param UpdateArticleRequest $request
     *
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|object
     */
    public function update($id, UpdateArticleRequest $request)
    {
        try {
            $articleService = new ArticleService();
            $articleService->update($id, $request);

            Flash::success('Новината е обновена успешно.');

            return redirect(route('manage.article.index'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    /**
     * Remove the specified Article from storage.
     *
     * @param int $id
     *
     * @param DeleteArticleRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     *
     */
    public function destroy($id, DeleteArticleRequest $request)
    {
        try {

            $articleService = new ArticleService();
            $articleService->delete($id);

            Flash::success('Новината е изтрита успешно.');

            return redirect(route('manage.article.index'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function togglePublish($id, TogglePublishArticleRequest $request)
    {
        try {
            $articleService = new ArticleService();
            $article = $articleService->togglePublish($id);
            $actionName = $article->status == ArticleEnum::STATUS_DRAFT ? ' разпубликувахте':' публикувахте';
            \Alert::success('Успешно '.$actionName.' новина', 'Успех!');

            return redirect()->back();
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function slugify(Request $request)
    {
        return response()->json([
            'slug' => Str::slug($request->get('title')),
        ]);
    }


    public function deleteImage($articleId, $imageId, DeleteArticleRequest $request)
    {
        try {
            $articleService = new ArticleService();
            $articleService->deleteImage($articleId, $imageId);

            \Alert::success( 'Успех!', 'Успешно премахнахте снимка от новината.');

            return redirect()->route('manage.article.edit', $articleId);
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }

    }
}
