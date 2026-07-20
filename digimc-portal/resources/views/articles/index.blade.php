@extends('layouts.app')

@section('content')
    <section class="section content">

        <div class="container">
            <h1 class="pull-left">Новини</h1>
            <h1 class="pull-right">
                <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px"
                   href="{!! route('manage.article.create') !!}">Добави</a>
            </h1>


            <div class="clearfix"></div>

            @include('flash::message')

            <div class="clearfix"></div>
            <div class="box box-primary">
                <div class="box-body">
                    @include('articles.table')
                </div>
            </div>
            <div class="text-center">

            </div>
        </div>
    </section>
@endsection

