<?php

namespace App\Abstracts\DataTables;

use Yajra\DataTables\Services\DataTable;

abstract class BaseDataTable extends DataTable
{

    /**
     * Optional method if you want to use html builder.
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $builder = $this->builder()->columns($this->getColumns())->minifiedAjax();
        if (isset($this->hasAction) && $this->hasAction) {
            $builder = $builder->addAction(
                [
                    'width' => '30px',
                    'class' => '',
                    'printable' => false,
                    'title' => 'Действия',
                    'name' => 'action',
                ]
            );
        }

        if (isset($this->deferLoading)) {
            $builder = $builder->deferLoading($this->deferLoading);
        }

        return $builder->parameters(
            [
                'dom'            => 'Bfrtip',
                'scrollX' => false,
                'order'          => [[0, 'desc']],
                'buttons' => [
                    [
                        'extend' => 'collection',
                        'text' => '<i class="fa fa-download"></i> Експорт',
                        'buttons' => [
                            'csv',
                            'excel',
                        ],
                    ],
                ],
                "language" => [
                    "url" => "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Bulgarian.json",
                ],
            ]
        )->postAjax([]);
    }

    public function getColumns(): array
    {
        return [

        ];
    }

    public function getColumnTitles(): array
    {
        return array_map(function ($info) {
            return $info['title'];
        }, $this->getColumns());
    }

}
