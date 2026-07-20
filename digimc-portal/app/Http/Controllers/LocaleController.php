<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Persist the selected UI locale and redirect back.
     *
     * @param  Request  $request  expects 'locale' in ['bg','en']
     * @return RedirectResponse
     */
    public function switch(Request $request): RedirectResponse
    {
        try {
            $allowed = ['bg', 'en'];

            $request->validate([
                'locale' => ['required', 'string', 'in:' . implode(',', $allowed)],
            ]);

            session(['locale' => $request->string('locale')->toString()]);
            return back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }
}
