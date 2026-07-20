<?php

namespace App\Http\Controllers;

use App\Enums\SettingEnum;
use App\Http\Requests\AddFavoritesRequest;
use App\Http\Requests\RemoveFavoritesRequest;
use App\Models\CulturalObjectLike;
use App\Services\ProfileService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;


class FavoriteController extends Controller
{

    public function index()
    {
        try {
            $perPage = SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH);
            $service = new ProfileService();
            $favorites = $service->getUserFavoritesPaginated($perPage);
            $user_likes =\Auth::check() ? CulturalObjectLike::whereIn('cultural_object_id', $favorites->pluck('id'))->get() : collect([]);
            return view('profile.favorites', [
                'paginatedFavoriteObjects' => $favorites,
                'user_likes' => $user_likes,
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function addMultiple(AddFavoritesRequest $request)
    {
        try {

            $service = new ProfileService();
            $added = $service->addFavorites($request->object_ids, $request->user());

            return response()->json([
                'success' => true,
                'object_ids' => $added
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function removeMultiple(RemoveFavoritesRequest $request)
    {
        try {
            $service = new ProfileService();
            $removed = $service->removeFavorites($request->object_ids, $request->user());

            return response()->json([
                'success' => true,
                'object_ids' => $removed
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
