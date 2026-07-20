@extends('layouts.app')

@section('content')
    <section class="section content">
        <div class="container">
        <h1>Създаване на страница</h1>


        @if(!empty($errors))
            @if($errors->any())
                <ul class="alert alert-danger" style="list-style-type: none">
                    @foreach($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            @endif
        @endif

        <div class="box box-primary">
            <div class="box-body">
                {!! html()->form(action: route('manage.page.store'))->open() !!}
                @include('manage.pages.fields')

                <!-- Submit Field -->
                <div class="form-group col-sm-12 pt-4 d-flex justify-content-between">
                    <a href="{!! route('manage.page.index') !!}" class="btn btn-default">Откажи</a>
                    {!! html()->submit('Запази')->attributes(['class' => 'btn btn-primary']) !!}
                </div>

                {!! html()->form()->close() !!}
            </div>
        </div>
    </div>
    </section>
@endsection


@push('styles')
    <!-- Summernote -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <!-- Summernote -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

@endpush

