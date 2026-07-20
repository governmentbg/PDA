<?php

namespace App\DataTables;

use App\Abstracts\DataTables\BaseDataTable;
use App\Enums\PageEnum;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class PageDataTable extends BaseDataTable
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
                return view('manage.pages.datatables_actions', ['id' => $row->id, 'row' => $row]);
            })
            ->editColumn('status', function ($row) {
                $readableStatus = PageEnum::getReadableStatus($row->status);
                return $readableStatus;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $page = Page::query();

        return $this->applyScopes($page);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            'id' => ['name' => 'id', 'data' => 'id', 'title' => '#'],
            'title' => ['name' => 'title', 'data' => 'title', 'title' => 'Заглавие'],
            'sef_title' => ['name' => 'sef_title', 'data' => 'sef_title', 'title' => 'SEF Заглавие'],
            'status' => ['name' => 'status', 'data' => 'status', 'title' => 'Статус'],
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
        return 'page';
    }
}
