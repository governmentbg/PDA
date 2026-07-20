@extends('layouts.app')

@section('content')
    <section>

        <div class="container my-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <!-- Title -->
                            <h1 class="card-title text-center mb-4">{{ $article->title }}</h1>
                            <small class="text-muted"> {{ $article->published_at?->format('d.m.Y H:i') }}</small>
                            @if($article->image?->filepath)
                                <div class="d-flex flex-column flex-md-row align-items-start mb-3">
                                    <!-- Image -->
                                    <img src="{{ asset($article->image->filepath) }}"
                                         alt="{{ $article->title }}"
                                         class="img-fluid img-thumbnail me-md-3 mb-3 mb-md-0"
                                         style="max-width:200px; object-fit:cover;">

                                    <!-- Content and Type -->
                                    <div>
                                        <p class="card-text">{!! $article->content !!}</p>
                                        <small class="text-muted">
                                            {{ \App\Enums\ArticleTypeEnum::label($article->article_type_id) }}
                                        </small>
                                    </div>
                                </div>
                            @else
                                <!-- Full-width content if no image -->
                                <p class="card-text mb-2">{!! $article->content !!}</p>
                                <small class="text-muted">
                                    {{ \App\Enums\ArticleTypeEnum::label($article->article_type_id) }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
@endsection
