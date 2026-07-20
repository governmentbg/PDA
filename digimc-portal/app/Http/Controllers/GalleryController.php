<?php

namespace App\Http\Controllers;

use App\Models\CulturalObjectLike;
use App\Services\GalleryService;
use Illuminate\Http\Request;
class GalleryController extends Controller
{

    public function index()
    {
        try {
            $service = new GalleryService();
            $lists = $service->listForUser();
            $activeTab = request()->get('tab', 'my');

            return view('profile.galleries.index', [
                'publicGalleries' => $lists['public'],
                'pendingGalleries' => $lists['pending'],
                'myGalleries' => $lists['private'],
                'activeTab' => $activeTab,
            ]);
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function public()
    {
        try {
            $service = new GalleryService();
            $publicGalleries = $service->listPublic();
            return view('pages.gallery.index', compact('publicGalleries'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function view($id)
    {
        try {
            $service = new GalleryService();
            $gallery = $service->getPublicGalleryWithObjects($id);
            $user_likes =\Auth::check() ? CulturalObjectLike::whereIn('cultural_object_id', $gallery->objects->pluck('id'))->get() : collect([]);
            return view('pages.gallery.view', compact('gallery','user_likes'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function show(int $galleryId)
    {
        try {
            $service = new GalleryService();
            $gallery = $service->getGalleryWithObjects($galleryId);
            $user_likes =\Auth::check() ? CulturalObjectLike::whereIn('cultural_object_id', $gallery->objects->pluck('id'))->get() : collect([]);
            return view('profile.galleries.show', compact('gallery','user_likes'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $service = new GalleryService();
            $service->create($request);
            return redirect()
                ->route('profile.galleries.index')
                ->with('success', __('gallery.collection_created_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function update(Request $request, int $galleryId)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'string|nullable',
            ]);
            $service = new GalleryService();
            $request->merge(['gallery_id' => $galleryId]);
            $service->edit($request);
            return redirect()
                ->route('profile.galleries.index')
                ->with('success', __('gallery.collection_renamed_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function destroy(int $galleryId)
    {
        try {
            $request = new Request(['gallery_id' => $galleryId]);
            $service = new GalleryService();
            $service->delete($request);
            return redirect()
                ->route('profile.galleries.index')
                ->with('success', __('gallery.collection_deleted_success'));
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }



    public function addObjects(Request $request)
    {
        try {
            $service = new GalleryService();
            $galleryId = $request->input('gallery_id');
            $objectIds = $request->input('object_ids', []);

            $service->addObjects($galleryId, $objectIds);

            return response()->json(['success' => true,'message' => __('gallery.objects_added_to_collection')]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()]);
        }
    }


    public function removeObjects(Request $request)
    {
        try {
            $service = new GalleryService();
            $galleryId = $request->input('gallery_id');
            $objectIds = $request->input('object_ids', []);

            $service->removeObjects($galleryId, $objectIds);
            return response()->json(['success' => true, 'message' => __('gallery.objects_removed_successfully')]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()]);
        }
    }
}
