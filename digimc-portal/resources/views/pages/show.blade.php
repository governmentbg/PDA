@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1>{{ $page->title }}</h1>
        <div class="mt-3">
            {!! $page->content !!}
        </div>
    </div>
@endsection
