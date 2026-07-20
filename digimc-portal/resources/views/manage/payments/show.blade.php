@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Плащане #{{ $payment->id }}</h1>

        <table class="table table-bordered">
            <tr>
                <th>Потребител</th>
                <td>{{ $payment->user->email ?? '-' }}</td>
            </tr>
            <tr>
                <th>Платено на</th>
                <td>{{ optional($payment->paid_at)->format('d.m.Y H:i') }}</td>
            </tr>
            <tr>
                <th>Сума EUR</th>
                <td>{{ number_format($payment->total_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Сума BGN</th>
                <td>{{ number_format($payment->amount_bgn, 2) }}</td>
            </tr>
            <tr>
                <th>Статус</th>
                <td>{{ \App\Enums\PaymentStatusEnum::getReadableStatus($payment->status) }}</td>
            </tr>
            <tr>
                <th>Номер на Трансакция</th>
                <td>{{ $payment->external_transaction_id ?? '-' }}</td>
            </tr>
        </table>

        <h3>Файлове по културни обекти</h3>
        @php
            $grouped = $payment->items->groupBy(function($item){
                return $item->webResource->culturalObjects->pluck('id')->first() ?? 0;
            });
        @endphp

        @foreach($grouped as $coId => $items)
            @php $co = $items->first()->webResource->culturalObjects->first(); @endphp
            <h4>
                @if($co)
                    <a href="{{ route('cultural_object.view', $co->id) }}" target="_blank">{{ $co->title }}</a>
                @else
                    Без културен обект
                @endif
            </h4>
            <ul>
                @foreach($items as $item)
                    @php
                        $res = $item->webResource;
                        $resName = $res->identifier ?? $res->web_resource_address ?? 'Файл #' . $res->id;
                        $priceEur = number_format($item->price, 2);
                        $priceBgn = number_format($item->price_bgn, 2);
                    @endphp
                    <li>
                        <a href="{{ $co ? route('cultural_object.view', $co->id) . '?res=' . $res->id : '#' }}" target="_blank">
                            {{ $resName }}
                        </a>
                        - <strong>{{ $priceEur }} EUR</strong> / <strong>{{ $priceBgn }} BGN</strong>
                    </li>
                @endforeach
            </ul>
        @endforeach

        <a href="{{ route('manage.payments.index') }}" class="btn btn-secondary">Назад към списъка</a>
    </div>
    <br>
@endsection
