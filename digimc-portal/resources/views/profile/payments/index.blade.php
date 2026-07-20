@extends('layouts.app')

@section('content')

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h1>{{ __('payments.title') }}</h1>

        <form method="GET" class="mb-3 d-flex gap-2">
            <div class="mb-2">
                <label>{{ __('payments.from_date') }}</label>
                <input type="text" id="date_from_display" class="form-control"
                       value="{{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d.m.Y') : '' }}"
                       placeholder="{{ __('payments.placeholder_date') }}">
                <input type="hidden" name="date_from" id="date_from"
                       value="{{ request('date_from') ?? '' }}">
            </div>

            <div class="mb-2">
                <label>{{ __('payments.to_date') }}</label>
                <input type="text" id="date_to_display" class="form-control"
                       value="{{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d.m.Y') : '' }}"
                       placeholder="{{ __('payments.placeholder_date') }}">
                <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') ?? '' }}">
            </div>

            <div class="mb-2">
                <label>{{ __('payments.status') }}</label>
                <select name="status" class="form-control">
                    <option value="">{{ __('payments.all_statuses') }}</option>
                    @foreach(\App\Enums\PaymentStatusEnum::getReadableStatus() as $code => $label)
                        <option value="{{ $code }}" {{ request('status') == $code ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label></label>
                <button class="btn btn-primary form-control">{{ __('payments.filter') }}</button>
            </div>
            <div>
                <label></label>
                <a href="{{ route('profile.payments.index') }}" class=" form-control btn btn-secondary">{{ __('payments.clear') }}</a>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>{{ __('payments.code') }}</th>
                <th>{{ __('payments.sum_eur') }}</th>
                <th>{{ __('payments.sum_bgn') }}</th>
                <th>{{ __('payments.files') }}</th>
                <th>{{ __('payments.cultural_objects') }}</th>
                <th>{{ __('payments.status') }}</th>
                <th>{{ __('payments.date') }}</th>
                <th>{{ __('payments.actions') }}</th>
            </tr>
            </thead>

            <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_code }}</td>
                    <td>{{ number_format($payment->total_amount,2) }}</td>
                    <td>{{ number_format($payment->amount_bgn,2) }}</td>

                    <td>
                        @php $items = $payment->items; @endphp
                        @if($items->isEmpty())
                            -
                        @else
                            @php $first = $items->first()->webResource; @endphp
                            @if($first)
                                <a href="{{ route('cultural_object.view', $first->culturalObjects->first()?->id ?? 0) }}?res={{ $first->id }}" target="_blank">
                                    {{ $first->identifier ?? 'Файл #'.$first->id }}
                                </a>
                                @if($items->count() > 1)
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#paymentFilesModal{{ $payment->id }}">
                                        <small>+{{ $items->count()-1 }} {{ __('payments.more') }}</small>
                                    </a>

                                    <div class="modal fade" id="paymentFilesModal{{ $payment->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('payments.payment_files_for', ['code' => $payment->payment_code]) }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-sm">
                                                        <thead>
                                                        <tr>
                                                            <th>{{ __('payments.file') }}</th>
                                                            <th>{{ __('payments.cultural_objects') }}</th>
                                                            <th>{{ __('payments.sum_eur') }}</th>
                                                            <th>{{ __('payments.sum_bgn') }}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($items as $item)
                                                            @php
                                                                $res = $item->webResource;
                                                                $co = $res?->culturalObjects->first();
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    @if($res)
                                                                        <a href="{{ $co ? route('cultural_object.view',$co->id).'?res='.$res->id:'#' }}" target="_blank">
                                                                            {{ $res->identifier ?? 'Файл #'.$res->id }}
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($co)
                                                                        <a href="{{ route('cultural_object.view',$co->id) }}" target="_blank">{{ $co->title }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>{{ number_format($item->price,2) }}</td>
                                                                <td>{{ number_format($item->price_bgn,2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('payments.close') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    </td>
                    <td>
                        @php
                            $links = [];
                            foreach($items as $item){
                                $res = $item->webResource;
                                if(!$res) continue;
                                foreach($res->culturalObjects as $co){
                                    $links[$co->id] = "<a href='".route('cultural_object.view',$co->id)."' target='_blank'>{$co->title}</a>";
                                }
                            }
                        @endphp
                        {!! $links ? implode('<br>', $links) : '-' !!}
                    </td>
                    <td>{{ \App\Enums\PaymentStatusEnum::getReadableStatus($payment->status) }}</td>
                    <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                    @if($payment->status === \App\Enums\PaymentStatusEnum::PENDING)
                        <form action="{{ route('profile.payments.suspend', $payment->id) }}" method="POST"
                              onsubmit="return confirm('{{ __('messages.payment.confirm_suspend') }}')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fa fa-times"></i> {{ __('payments.suspend') }}
                            </button>
                        </form>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">{{ __('payments.no_payments') }}</td></tr>
            @endforelse
            </tbody>
        </table>

        {{ $payments->links() }}

    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/css/bootstrap-datepicker3.min.css">
@endpush
@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/js/bootstrap-datepicker.min.js"></script>
        <script>
            $(document).ready(function() {
            $('#date_from_display, #date_to_display').datepicker({
                format: 'dd.mm.yyyy',
                autoclose: true,
                todayHighlight: true,
                orientation: "bottom auto"
            }).on('changeDate', function(e) {
                let hiddenId = $(this).attr('id').replace('_display', '');
                let selectedDate = e.format('yyyy-mm-dd');
                $('#' + hiddenId).val(selectedDate);
            });
        });
    </script>

@endpush
