<?php

namespace App\Services;

use App\Enums\PageEnum;
use App\Models\Page;
use Illuminate\Http\Request;

class PageService
{
    public function store(Request $request): Page
    {
        $input = $request->all();
        $input['status'] = PageEnum::STATUS_DRAFT;

        /** @var Page $page */
        $page = Page::create($input);

        return $page;
    }

    public function update($id, Request $request): Page
    {
        /** @var Page|null $page */
        $page = Page::find($id);

        if ($page === null) {
            throw new \Exception('Страницата не е намерена');
        }

        $page->fill($request->all());
        $page->save();

        return $page;
    }

    public function delete($id)
    {
        /** @var Page|null $page */
        $page = Page::find($id);

        if ($page === null) {
            throw new \Exception('Страницата не е намерена');
        }

        $page->delete();
    }

    public function togglePublish($id): Page
    {
        /** @var Page|null $page */
        $page = Page::find($id);

        if ($page === null) {
            throw new \Exception('Страницата не е намерена');
        }

        if ($page->status === PageEnum::STATUS_DRAFT) {
            $page->status = PageEnum::STATUS_PUBLISHED;
        } else {
            $page->status = PageEnum::STATUS_DRAFT;
        }

        $page->save();

        return $page;
    }
}
