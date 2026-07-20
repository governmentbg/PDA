<?php

namespace App\DataTables;

use App\Abstracts\DataTables\BaseDataTable;
use App\Models\Payment;
use App\Enums\PaymentStatusEnum;
use Illuminate\Http\JsonResponse;

class PaymentDataTable extends BaseDataTable
{
    public bool $hasAction = true;

    public function ajax(): JsonResponse
    {
        return datatables()
            ->eloquent($this->query())
            ->editColumn('user', fn($row) => optional($row->user)->email)

            ->addColumn('amount_eur', fn($row) => number_format($row->total_amount, 2))
            ->addColumn('amount_bgn', fn($row) => number_format($row->amount_bgn, 2))

            ->addColumn('files', function ($row) {
                $items = $row->items;
                if ($items->isEmpty()) return '-';
                $first = $items->first()->webResource;
                if (!$first) return '-';
                $labelName = $first->identifier ?? $first->web_resource_address ?? 'Файл #' . $first->id;
                $co = $first->culturalObjects->first();
                $url = $co ? route('cultural_object.view', $co->id) . '?res=' . $first->id : '#';
                $label = "<a href='{$url}' target='_blank'>{$labelName}</a>";
                if ($items->count() > 1) $label .= ' <small>+'.($items->count() - 1).' още</small>';
                return $label;
            })
            ->addColumn('cultural_objects', function ($row) {
                $links = [];
                foreach ($row->items as $item) {
                    $res = $item->webResource;
                    if (!$res) continue;
                    foreach ($res->culturalObjects as $co) {
                        $links[] = "<a href='".route('cultural_object.view', $co->id)."' target='_blank'>{$co->title}</a>";
                    }
                }
                $links = array_unique($links);
                return $links ? implode('<br>', $links) : '-';
            })

            ->editColumn('status', fn($row) => PaymentStatusEnum::getReadableStatus($row->status))

            ->editColumn('created_at', fn($row) => $row->created_at->format('d.m.Y H:i'))
            ->editColumn('expires_at', fn($row) => $row->expires_at->format('d.m.Y H:i'))
            ->editColumn('paid_at', function($row) {
                return $row->paid_at ? $row->paid_at->format('d.m.Y H:i') : '-';
            })

            ->filterColumn('user', function($query, $keyword) {
                $query->whereHas('user', function($q) use ($keyword) {
                    $q->where('email', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('action', function ($row) {
                return view('manage.payments.datatables_actions', [
                    'id' => $row->id,
                    'row' => $row
                ]);
            })

            ->rawColumns(['files', 'cultural_objects', 'action'])
            ->make(true);
    }

    public function query()
    {
        return Payment::withTrashed()
            ->with(['user', 'items.webResource.culturalObjects'])
            ->select('payment.*');
    }

    public function getColumns(): array
    {
        return [
             'id' => ['title' => '#', 'orderable' => true],
             'external_transaction_id' => ['title' => 'Номер на Трансакция', 'orderable' => true],
             'user' => ['name' => 'user.email', 'data' => 'user', 'title' => 'Потребител', 'orderable' => true],
             'amount_eur' => ['name' => 'total_amount', 'data' => 'amount_eur', 'title' => 'Сума EUR', 'orderable' => true],
             'amount_bgn' => ['name' => 'total_amount', 'data' => 'amount_bgn', 'title' => 'Сума BGN', 'orderable' => true],
             'files' => ['title' => 'Файлове', 'orderable' => false],
             'cultural_objects' => ['title' => 'Културни обекти', 'orderable' => false],
             'status' => ['title' => 'Статус', 'orderable' => true],
             'payment_code' => ['title' => 'Код', 'orderable' => true],
             'created_at' => ['title' => 'Създадено на', 'orderable' => true],
             'expires_at' => ['title' => 'Валидност на кода', 'orderable' => true],
             'paid_at' => ['title' => 'Платено на', 'orderable' => true],
        ];
    }

    public function builder(): \Yajra\DataTables\Html\Builder
    {
        $builder = parent::builder();
        return $builder->parameters([
            'retrieve' => true,
            'order' => [[9, 'desc']],
            'responsive' => true,
            'autoWidth' => false,
        ]);
    }

    protected function filename(): string
    {
        return 'payments';
    }
}
