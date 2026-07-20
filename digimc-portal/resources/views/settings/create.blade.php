@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="container">
        <h1>
            Настройки
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
                        {!! html()->form(action: route('manage.settings.store'))->open() !!}

                        @include('settings.fields')

                        {!! html()->form()->close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
