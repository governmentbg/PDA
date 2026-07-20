<?php

namespace App\Http\Controllers;

use App\Enums\CodelistEnum;
use App\Enums\CulturalObjectEnum;
use App\Enums\GalleryEnum;
use App\Enums\SettingEnum;
use App\Models\CodeValue;
use App\Models\CulturalObject;
use App\Models\CulturalObjectLike;
use App\Models\Gallery;
use App\Models\GalleryCulturalObject;
use App\Models\Provider;
use App\Models\WebResource;
use App\Services\CulturalObjectService;
use Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CulturalObjectController extends Controller
{

    /**
     * @return View|RedirectResponse
     * @phpstan-return View|RedirectResponse
     */
    public function index()
    {
        try {
            $paginatedObjects = CulturalObject::with('provider')
                ->orderBy('id', 'DESC')
                ->paginate(SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH));

            $data = [
                'culturalObjects' => $paginatedObjects,
            ];

            $data['user_likes'] = \Auth::check() ? CulturalObjectLike::whereIn('cultural_object_id', $paginatedObjects->pluck('id'))->get() : collect([]);

            return view('pages.cultural_object.index', $data);
        } catch (\Exception $exception) {
            \Alert::error(__('errors.the_object_was_not_found'));
            return redirect()->back();
        }
    }

    /**
     * @param  int  $culturalObjectId
     * @return View|RedirectResponse
     * @phpstan-return View|RedirectResponse
     */
    public function view($culturalObjectId)
    {
        try {
            /** @var CulturalObject|null $object */
            $object = CulturalObject::where(['id' => $culturalObjectId])->firstOrFail();

            if (!is_null($object)) {
                $object->load(CulturalObjectEnum::loadRelations($object->type));
            }

            $galleryIds = GalleryCulturalObject::where(
                'cultural_object_id',
                $object->id
            )->pluck('gallery_id');

            $publicGalleries = Gallery::whereIn('id', $galleryIds)
                ->where('status', GalleryEnum::STATUS_PUBLIC)
                ->with('user')
                ->withCount('cultural_objects')
                ->paginate(SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH))
                ->withQueryString();
            $mimeTypesLabels = CodeValue::labelsForCodes(CodeValue::where('codelist_code', CodelistEnum::MIME_TYPE)->pluck('code')->toArray());

            $data = [
                'culturalObject' => $object,
                'provider' => Provider::where('id', $object->cultural_object_provided_by)->first(),
                'export' => app(CulturalObjectService::class)->makeCsvExportPayload($object),
                'publicGalleries' => $publicGalleries,
                'mimeTypesLabels' => $mimeTypesLabels,
            ];
            return view('pages.cultural_object.view', $data);
        } catch (\Exception $exception) {
            \Alert::error(__('errors.the_object_was_not_found'));
            return redirect()->back();
        }
    }

    /**
     * @param  int|string  $culturalObjectId
     * @return StreamedResponse|RedirectResponse
     */
    public function exportCsv($culturalObjectId)
    {
        try {
            $object = CulturalObject::where(['id' => $culturalObjectId])->firstOrFail();

            $object->load(CulturalObjectEnum::loadRelations($object->type));

            $service = new CulturalObjectService();
            return $service->streamCsv($object);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    /**
     * @param int $culturalObjectId
     * @param Request $request
     * @return StreamedResponse|RedirectResponse
     */
    public function download(int $culturalObjectId, Request $request): StreamedResponse|RedirectResponse
    {
        try {
            /** @var CulturalObject $itemToDownload */
            $itemToDownload = CulturalObject::with(['has_web_view_resource'])->findOrFail($culturalObjectId);

            $resourceId = $request->query('res');
            if (is_array($resourceId)) {
                $resourceId = null;
            }

            /** @var int|string|null $resourceId */
            $service = new CulturalObjectService();
            return $service->downloadObject($itemToDownload, $resourceId);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }
    public function getTiffPage(string $web_id, int $page_number)
    {
        try {
            $service = new CulturalObjectService();
            $data = $service->getTiffPageInfo($web_id, $page_number);

            $transformedData = $service->transformIIIFJson($web_id, $page_number, $data);

            return response()->json($transformedData);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'error' => 'IIIF Proxy Error: ' . $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    public function proxyTiffTile(string $web_id, int $page_number, string $iiif_path)
    {
        try {
            $service = new CulturalObjectService();
            return $service->proxyTileRequest($web_id, $page_number, $iiif_path);

        } catch (\Exception $e) {
            return response('Proxy Error: ' . $e->getMessage(), $e->getCode() ?: 500)
                ->header('Content-Type', 'text/plain');
        }
    }

    public function signVideo(Request $request)
    {
        $request->validate([
            'file_key' => ['required', 'string'],
        ]);

        $service = new CulturalObjectService();

        $signed = $service->getSignedUrl($request->file_key);

        if (!$signed) {
            return response()->json(['message' => 'Unable to sign'], 500);
        }

        return response()->json($signed);
    }
}
