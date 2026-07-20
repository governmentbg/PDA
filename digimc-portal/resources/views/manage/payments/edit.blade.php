@extends('layouts.app')
@php use App\Enums\PaymentStatusEnum; @endphp

@section('content')
    <div class="container">
        <h1>Редакция на плащане #{{ $payment->id }}</h1>

        <form method="POST" action="{{ route('manage.payments.update', $payment) }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label>Статус</label>
                <select name="status" class="form-control">
                    @foreach(PaymentStatusEnum::getReadableStatus() as $key => $label)
                        <option value="{{ $key }}" @selected($payment->status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Transaction ID</label>
                <input type="text" name="external_transaction_id" class="form-control"
                       value="{{ old('external_transaction_id', $payment->external_transaction_id) }}">
            </div>

            <button class="btn btn-success">Запази</button>
            <a href="{{ route('manage.payments.show', $payment) }}" class="btn btn-secondary">Отказ</a>
        </form>
    </div>
    <br>
@endsection
