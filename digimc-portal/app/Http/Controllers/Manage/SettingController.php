<?php

namespace App\Http\Controllers\Manage;

use App\DataTables\SettingDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSettingRequest;
use App\Http\Requests\DeleteSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use Flash;
use Illuminate\Http\Request;
use Response;

class SettingController extends Controller
{
    /**
     * Display a listing of the Setting.
     *
     * @param SettingDataTable $settingDataTable
     * @return \Illuminate\View\View
     */
    public function index(SettingDataTable $settingDataTable)
    {
        return $settingDataTable->render('settings.index');
    }

    /**
     * Show the form for creating a new Setting.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('settings.create');
    }

    /**
     * Store a newly created Setting in storage.
     *
     * @param CreateSettingRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateSettingRequest $request)
    {
        $input = $request->all();

        Setting::create($input);

        Flash::success('Настройката беше запазена успешно.');

        return redirect(route('manage.settings.index'));
    }

    /**
     * Show the form for editing the specified Setting.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        /** @var Setting|null $setting */
        $setting = Setting::find($id);

        if ($setting === null) {
            Flash::error('Настройката не е намерена.');

            return redirect(route('manage.settings.index'));
        }

        return view('settings.edit')->with('setting', $setting);
    }

    /**
     * Update the specified Setting in storage.
     *
     * @param  int              $id
     * @param UpdateSettingRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update($id, UpdateSettingRequest $request)
    {
        /** @var Setting|null $setting */
        $setting = Setting::find($id);

        if ($setting === null) {
            Flash::error('Настройката не е намерена.');

            return redirect(route('manage.settings.index'));
        }

        $setting->fill($request->all());
        $setting->save();

        Flash::success('Настройката беше обновена успешно.');

        return redirect(route('manage.settings.index'));
    }

    /**
     * Remove the specified Setting from storage.
     *
     * @param  int $id
     *
     * @param  DeleteSettingRequest $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id, DeleteSettingRequest $request)
    {
        /** @var Setting|null $setting */
        $setting = Setting::find($id);

        if ($setting === null) {
            Flash::error('Настройката не е намерена.');

            return redirect(route('manage.settings.index'));
        }

        $setting->delete();

        Flash::success('Настройката беше изтрита успешно.');

        return redirect(route('manage.settings.index'));
    }
}
