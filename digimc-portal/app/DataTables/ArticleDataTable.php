<?php

namespace App\DataTables;

use App\Abstracts\DataTables\BaseDataTable;
use App\Enums\ArticleEnum;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

class ArticleDataTable extends BaseDataTable
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
                return view('articles.datatables_actions', ['id' => $row->id, 'row' => $row]);
            })
            ->editColumn('article_type_id', function ($row) {
                return $row->type->name;
            })
            ->editColumn('status', function ($row) {
                $readableStatus = ArticleEnum::getReadableStatus($row->status);
                if(!is_null($row->published_at))
                {
                    $readableStatus.=" на:<br>".$row->published_at->format('H:i:s d.m.Y');
                }
                return $readableStatus;
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
        $article = Article::query()->with(['image', 'type']);

        return $this->applyScopes($article);
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
            'article_type_id' => ['name' => 'article_type_id', 'data' => 'article_type_id', 'title' => 'Тип'],
            'status' => ['name' => 'status', 'data' => 'status', 'title' => 'Статус'],
            'title' => ['name' => 'title', 'data' => 'title', 'title' => 'Заглавие'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'article';
    }
}
