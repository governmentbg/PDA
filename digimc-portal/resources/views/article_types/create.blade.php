@extends('layouts.app')

@section('content')
    <section class="section content">
        <div class="container">
            <h1>
                Типове новини
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
                        {!! html()->form(action: route('manage.article_type.store'))->open() !!}

                        @include('article_types.fields')

                        {!! html()->form()->close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
