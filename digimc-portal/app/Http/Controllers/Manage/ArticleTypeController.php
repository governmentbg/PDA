<?php

namespace App\Http\Controllers\Manage;

use App\DataTables\ArticleTypeDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateArticleTypeRequest;
use App\Http\Requests\DeleteArticleTypeRequest;
use App\Http\Requests\UpdateArticleTypeRequest;
use App\Models\Article;
use App\Models\ArticleType;
use Flash;
use Illuminate\Http\Request;
use Response;

class ArticleTypeController extends Controller
{
    /**
     * Display a listing of the ArticleType.
     *
     * @param ArticleTypeDataTable $articleTypeDataTable
     * @return \Illuminate\View\View
     */
    public function index(ArticleTypeDataTable $articleTypeDataTable)
    {
        return $articleTypeDataTable->render('article_types.index');
    }

    /**
     * Show the form for creating a new ArticleType.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('article_types.create');
    }

    /**
     * Store a newly created ArticleType in storage.
     *
     * @param CreateArticleTypeRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(CreateArticleTypeRequest $request)
    {
        $input = $request->all();

        ArticleType::create($input);

        Flash::success('Тип новина запазено успешно!');

        return redirect(route('manage.article_type.index'));
    }

    /**
     * Show the form for editing the specified ArticleType.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        /** @var ArticleType|null $articleType */
        $articleType = ArticleType::find($id);

        if ($articleType === null) {
            Flash::error('Тип новина не е намерена!');

            return redirect(route('manage.article_type.index'));
        }

        return view('article_types.edit')->with('articleType', $articleType);
    }

    /**
     * Update the specified ArticleType in storage.
     *
     * @param  int              $id
     * @param UpdateArticleTypeRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update($id, UpdateArticleTypeRequest $request)
    {
        /** @var ArticleType|null $articleType */
        $articleType = ArticleType::find($id);

        if ($articleType === null) {
            Flash::error('Тип новина не е намерена!');

            return redirect(route('manage.article_type.index'));
        }

        $articleType->fill($request->all());
        $articleType->save();

        Flash::success('Тип новина е обновена успешно.');

        return redirect(route('manage.article_type.index'));
    }

    /**
     * Remove the specified ArticleType from storage.
     *
     * @param  int $id
     *
     * @param  DeleteArticleTypeRequest $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy($id, DeleteArticleTypeRequest $request)
    {
        /** @var ArticleType|null $articleType */
        $articleType = ArticleType::find($id);

        if ($articleType === null) {
            Flash::error('Тип новина не е намерена!');

            return redirect(route('manage.article_type.index'));
        }

        if(Article::where('article_type_id', $id)->count() > 0){
            Flash::error('Не може да триете типове, докато има публикувани новини разпределени към тях.');

            return redirect(route('manage.article_type.index'));
        }
        $articleType->delete();

        Flash::success('Тип новина е изтрита успешно.');

        return redirect(route('manage.article_type.index'));
    }

}
