<?php

namespace App\Http\Controllers\Manage;

use App\DataTables\PageDataTable;
use App\Enums\PageEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePageRequest;
use App\Http\Requests\DeletePageRequest;
use App\Http\Requests\TogglePublishPageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use App\Services\PageService;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(PageDataTable $dataTable)
    {
        return $dataTable->render('manage.pages.index');
    }

    public function create()
    {
        return view('manage.pages.create');
    }

    public function store(CreatePageRequest $request)
    {
        try {
            $service = new PageService();
            $service->store($request);

            Flash::success('Страницата е запазена успешно.');
            return redirect()->route('manage.page.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $page = Page::find($id);

        if (empty($page)) {
            Flash::error('Страницата не е намерена');
            return redirect()->route('manage.page.index');
        }

        return view('manage.pages.edit', compact('page'));
    }

    public function update($id, UpdatePageRequest $request)
    {
        try {
            $service = new PageService();
            $service->update($id, $request);

            Flash::success('Страницата е обновена успешно.');
            return redirect()->route('manage.page.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function destroy($id, DeletePageRequest $request)
    {
        try {
            $service = new PageService();
            $service->delete($id);

            Flash::success('Страницата е изтрита успешно.');
            return redirect()->route('manage.page.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function togglePublish($id, TogglePublishPageRequest $request)
    {
        try {
            $service = new PageService();
            $page = $service->togglePublish($id);

            $action = $page->status === PageEnum::STATUS_DRAFT ? 'разпубликувахте' : 'публикувахте';
            \Alert::success("Успешно $action страницата", "Успех!");

            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function slugify(Request $request)
    {
        return response()->json([
            'slug' => Str::slug($request->get('title')),
        ]);
    }
}
