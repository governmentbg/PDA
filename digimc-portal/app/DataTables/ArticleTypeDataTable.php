<?php

namespace App\DataTables;

use App\Abstracts\DataTables\BaseDataTable;
use App\Models\ArticleType;
use Illuminate\Http\JsonResponse;

class ArticleTypeDataTable extends BaseDataTable
{
    public bool $hasAction = true;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->addColumn('action', 'article_types.datatables_actions')
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $article_type = ArticleType::query();

        return $this->applyScopes($article_type);
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
    public function getColumns(): array
    {
        return [
            'id' => ['name' => 'id', 'data' => 'id', 'title' => '#'],
            'name' => ['name' => 'name', 'data' => 'name', 'title' => 'Име'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'article_type';
    }
}
