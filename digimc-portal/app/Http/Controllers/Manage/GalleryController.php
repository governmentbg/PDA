<?php

namespace App\Http\Controllers\Manage;

use App\DataTables\GalleryDataTable;
use App\DataTables\Scopes\GalleryStatusScope;
use App\Enums\GalleryEnum;
use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(GalleryDataTable $dataTable, Request $request)
    {

        try {
            $data = [];

            $status = $request->get('status', 'pending');

            if(!empty($status)) {
                $scope = new GalleryStatusScope();
                $scope->setFilter($status);
                $dataTable->addScope($scope);
            }

            $data['filtered_status'] = $status;

            return $dataTable->render('manage.gallery.index', $data);
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }
    }

    public function update(Request $request, Gallery $gallery)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:2000',
            ]);

            $service = new GalleryService();
            $service->update($gallery, $validated);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Галерията е обновена успешно.']);
            }

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function approve(Gallery $gallery)
    {
        try {
            $service = new GalleryService();
            $service->approve($gallery);
            return redirect()->back()->with('success', 'Галерията е одобрена!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function reject(Gallery $gallery, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $service = new GalleryService();
            $service->setPrivate($gallery, $request->reason);

            return redirect()->back()->with('success', 'Галерията е отказана!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

}
