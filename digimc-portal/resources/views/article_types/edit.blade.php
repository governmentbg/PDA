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
                        {{ html()->modelForm($articleType, 'patch' , route('manage.article_type.update', $articleType->id))->class('col-sm-12 form-row')->open() }}

                        @include('article_types.fields')

                        {{ html()->closeModelForm() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
