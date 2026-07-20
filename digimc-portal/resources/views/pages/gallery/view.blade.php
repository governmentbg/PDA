@extends('layouts.app')

@section('content')
    <section class="cultural section">
        <div class="container">
            <h2>{{ $gallery->name }}</h2>
            <p class="text-muted">by {{ $gallery->user->name }}</p>
            @if(!empty($gallery->description))
                <p class="text-muted" style="white-space: pre-wrap; word-wrap: break-word;">
                    {{ $gallery->description }}
                </p>
            @endif
            <div class="row gy-4">
                <!-- Start object -->
                <x-cultural-objects-list :cultural-objects="$gallery->objects" :user-likes="$user_likes"/>
                <!-- End object -->
            </div>
            <div class="d-flex justify-content-center mt-5">
                {{ $gallery->objects->links() }}
            </div>
        </div>
    </section>
@endsection
