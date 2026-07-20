@extends('layouts.app')

@section('content')
    <section class="section content">
        <div class="container">
            <h1>Плащания</h1>

            @include('flash::message')

            <div class="box box-primary">
                <div class="box-body">
                    @include('manage.payments.table')
                </div>
            </div>
        </div>
    </section>
@endsection
