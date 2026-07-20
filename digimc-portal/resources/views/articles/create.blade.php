@extends('layouts.app')

@section('content')
    <section class="section content">
        <div class="container">
            <h1>
                Новина
            </h1>


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
                    <div class="row">
                        {!! html()->form(action: route('manage.article.store'))->attribute('enctype','multipart/form-data')->open() !!}

                        @include('articles.fields')

                        {!! html()->form()->close() !!}
                    </div>
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
