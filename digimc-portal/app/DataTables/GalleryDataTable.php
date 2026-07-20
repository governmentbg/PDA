<?php

namespace App\DataTables;

use App\Abstracts\DataTables\BaseDataTable;
use App\Enums\GalleryEnum;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;

class GalleryDataTable extends BaseDataTable
{
    public bool $hasAction = true;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('action', function ($row) {
                return view('manage.gallery.datatables_actions', ['id' => $row->id, 'row' => $row]);
            })
            ->editColumn('description', function ($row) {
                return $row->description
                    ? \Illuminate\Support\Str::limit(strip_tags($row->description), 50, '...')
                    : '-';
            })
            ->editColumn('status', function ($row) {
                return GalleryEnum::getReadableStatus($row->status);
            })
            ->editColumn('requested_at', function($row) {
                return $row->requested_at ? \Carbon\Carbon::parse($row->requested_at)->format('d.m.Y H:i') : '-';
            })
            ->editColumn('published_at', function($row) {
                return $row->published_at ? \Carbon\Carbon::parse($row->published_at)->format('d.m.Y H:i') : '-';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $gallery = Gallery::query()
            ->with('user')
            ->withCount('cultural_objects');

        if (!request()->has('order') || empty(request()->get('order'))) {
            $gallery->orderByRaw("FIELD(status, ?, ?, ?)", [
                GalleryEnum::STATUS_PENDING,
                GalleryEnum::STATUS_PUBLIC,
                GalleryEnum::STATUS_PRIVATE
            ])->orderBy('created_at', 'desc');
        }

        return $this->applyScopes($gallery);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            'id' => ['name' => 'id', 'data' => 'id', 'title' => '#', 'orderable' => true, 'searchable' => true],
            'status' => ['name' => 'status', 'data' => 'status', 'title' => 'Статус', 'orderable' => true, 'searchable' => true],
            'name' => ['name' => 'name', 'data' => 'name', 'title' => 'Наименование', 'orderable' => true, 'searchable' => true],
            'description' => ['name' => 'description', 'data' => 'description', 'title' => 'Описание', 'orderable' => true, 'searchable' => true],
            'user.email' => ['name' => 'user.email', 'data' => 'user.email', 'title' => 'Потребител', 'orderable' => true, 'searchable' => true],
            'requested_at' => ['name' => 'requested_at', 'data' => 'requested_at', 'title' => 'Дата на заявяване за публикуване', 'orderable' => true, 'searchable' => false],
            'published_at' => ['name' => 'published_at', 'data' => 'published_at', 'title' => 'Дата на публикуване', 'orderable' => true, 'searchable' => false],
            'cultural_objects_count' => ['name' => 'cultural_objects_count', 'data' => 'cultural_objects_count', 'title' => 'Брой обекти', 'orderable' => true, 'searchable' => false],
        ];
    }

    public function builder(): \Yajra\DataTables\Html\Builder
    {
        $builder = parent::builder();

        return $builder->parameters([
            'retrieve' => true,
        ]);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'gallery';
    }
}
