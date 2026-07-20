<?php

namespace App\Http\Controllers;

use App\Enums\CodelistEnum;
use App\Enums\SettingEnum;
use App\Models\CodeValue;
use App\Models\CulturalObjectLike;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        try {

            $query = $request->get('q', '');

            $service = new SearchService();

            $advancedFilters = $service->extractAdvancedFilters($request->all());
            $quickFilters    = $service->extractQuickFilters($request->all());


            $facets = $service->getFacets($advancedFilters, $quickFilters);
            $perPage = min(SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH), 1000);


            $results = $service->search($query, $perPage, $advancedFilters, $quickFilters);
            $culturalObjectIds = collect($results->items())->pluck('id');

            $data = \Auth::check()
                ? CulturalObjectLike::whereIn('cultural_object_id', $culturalObjectIds)->get()
                : collect([]);

            $availableThemes    = $facets['theme'] ?? [];
            $availableTypes     = $facets['type'] ?? [];
            $availableRights    = $facets['rights_holder'] ?? [];
            $availableProviders = $facets['provider'] ?? [];
            $availableCountries = $facets['country_of_origin'] ?? [];

            $themeLabels = CodeValue::labelsForCodes($availableThemes);
            $mediaTypeLabels = CodeValue::labelsForCodes($availableTypes);
            $rightsLabels = CodeValue::labelsForCodes($availableRights);
            $providerLabels = CodeValue::labelsForCodes($availableProviders);
            $countryLabels = CodeValue::labelsForCodes($availableCountries);

            $avaliableTypesAdvanced = CodeValue::where('codelist_code', CodelistEnum::CULTURAL_OBJECT_TYPE)->pluck('code')->toArray();
            $objectTypeLabels = CodeValue::labelsForCodes($avaliableTypesAdvanced);
            $finalTypesForDropdown = array_combine($avaliableTypesAdvanced, $objectTypeLabels);

            $mimeTypes = CodeValue::where('codelist_code', CodelistEnum::MIME_TYPE)->pluck('code')->toArray();
            $mimeTypesLabels = CodeValue::labelsForCodes($mimeTypes);

            $searchInformationText = match(app()->getLocale()) {
                'en'    => SettingEnum::getValueByKeyword(SettingEnum::SEARCH_INFORMATION_TEXT_EN),
                default => SettingEnum::getValueByKeyword(SettingEnum::SEARCH_INFORMATION_TEXT_BG),
            };

            return view('pages.search.index', [
                'query' => $query,
                'culturalObjects' => $results,
                'user_likes' => $data,
                'advancedFilters' => $advancedFilters,
                'availableThemes' => $availableThemes,
                'availableTypes' => $availableTypes,
                'availableRights' => $availableRights,
                'availableProviders' => $availableProviders,
                'availableCountries' => $availableCountries,

                'themeLabels' => $themeLabels,
                'mediaTypeLabels' => $mediaTypeLabels,
                'rightsLabels' => $rightsLabels,
                'providerLabels' => $providerLabels,
                'countryLabels' => $countryLabels,
                'objectTypes' => $finalTypesForDropdown,
                'mimeTypes' => $mimeTypesLabels,
                'searchInformationText' => $searchInformationText,
            ]);
        } catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('swal_error', $exception->getMessage());

        }
    }

    public function exportCsv(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $filename = $request->get('filename', 'search_results_' . now()->format('Ymd_His')) . '.csv';

            $service = new SearchService();

            $advancedFilters = $service->extractAdvancedFilters($request->all());
            $quickFilters    = $service->extractQuickFilters($request->all());


            $callback = $service->prepareCsvExport($query, $advancedFilters, $quickFilters);

            return response()->stream($callback, 200, [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);

        } catch (\Exception $exception) {
            return back()->withErrors([$exception->getMessage()]);
        }
    }
}
