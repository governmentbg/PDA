<?php

namespace App\Http\Controllers;

use App\Enums\PageEnum;
use App\Models\Page;

class PageController extends Controller
{
    public function show(string $sef_title)
    {
        try {
            $page = Page::where('sef_title', $sef_title)
                ->where('status', PageEnum::STATUS_PUBLISHED)
                ->firstOrFail();

            return view('pages.show', compact('page'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors(__('general.no_results_found'));
        }
    }
}
