<?php

namespace App\Http\Controllers;

use App\Enums\SettingEnum;
use App\Models\CulturalObject;
use App\Models\CulturalObjectLike;
use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index()
    {
        try {
            $data = [
                'providers' => Provider::orderBy(
                    'id',
                    'DESC'
                )->paginate(SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH)),
            ];

            return view('pages.provider.index', $data);
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function view($providerId)
    {
        try {
            $provider = Provider::where(['id' => $providerId])->first();
            if (!$provider) {
                throw new \Exception(__('general.no_results_found'));
            }

            $culturalObjects = CulturalObject::with(['has_web_view_resource','provider'])
                ->where('cultural_object_provided_by', $provider->id)
                ->paginate(SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH));

            $userLikes = collect([]);

            if ($culturalObjects->count() > 0) {
                $userLikes = \Auth::check()
                    ? CulturalObjectLike::whereIn('cultural_object_id', $culturalObjects->pluck('id'))->get()
                    : collect([]);
            }

            $data = [
                'provider' => $provider,
                'cultural_objects' => $culturalObjects,
                'user_likes' => $userLikes,
            ];
            return view('pages.provider.view', $data);
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }
}
