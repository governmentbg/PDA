<?php

namespace App\DataTables;

use App\Abstracts\DataTables\BaseDataTable;
use App\Models\Setting;
use Form;
use Illuminate\Http\JsonResponse;

class SettingDataTable extends BaseDataTable
{
    public bool $hasAction = true;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax():JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('keyword', function($row) {
                return \App\Enums\SettingEnum::getHumanReadableName($row->keyword);
            })
            ->addColumn('action', 'settings.datatables_actions')
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $settings = Setting::query();

        return $this->applyScopes($settings);
    }


    public function builder(): \Yajra\DataTables\Html\Builder
    {
        $builder = parent::builder();

        return $builder->parameters([
            'retrieve' => true,
        ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    public function getColumns():array
    {
        return [
            'id' => ['name' => 'id', 'data' => 'id', 'title' => '#'],
            'keyword' => ['name' => 'keyword', 'data' => 'keyword', 'title' => 'Настройка'],
            'value' => ['name' => 'value', 'data' => 'value', 'title' => 'Стойност']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename():string
    {
        return 'settings';
    }
}
