@extends('layouts.app')

@section('content')
    <section class="services section">
        @if(!isset($articles) || $articles->count() == 0)
            <div class="text-center">
                {{ __('article.no_articles_added') }}
            </div>
        @else

            <div class="container my-4">
                    <div class="row gy-4">


                    @foreach($articles as $key => $article)
                        @if(!empty($article->image))

                            <!-- Article with image -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="service-card">
                                        <div>
                                            <img src="{{asset($article->image->filepath)}}"
                                                 alt="{{$article->title}}"
                                                 class="img-thumbnail me-3 img-fluid"
                                                 style="width:100%; max-height:80px; object-fit:cover;">
                                        </div>
                                        <h3>{{$article->title}}</h3>
                                        <small class="text-muted">
                                            {{ \App\Enums\ArticleTypeEnum::label($article->article_type_id) }} | {{ $article->published_at?->format('d.m.Y H:i') }}
                                        </small>
                                        <p style="word-wrap: break-word; overflow-wrap: break-word;">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($article->content ?? ''), 200, '...') }}
                                        </p>
                                        <a href="{{route('article.view', ['id' => $article->id, 'slug' => $article->slug])}}" class="service-link">
                                            {{ __('article.read_more') }}
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <!-- End Service Card -->

                        @else
                            <!-- Article without image -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="service-card">

                                        <h3>{{$article->title}}</h3>
                                        <small class="text-muted">
                                            {{ \App\Enums\ArticleTypeEnum::label($article->article_type_id ?? $article->type_id ?? $article->type) }} | {{ $article->published_at?->format('d.m.Y H:i') }}
                                        </small>
                                        <br>
                                        <p style="word-wrap: break-word; overflow-wrap: break-word;">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($article->content ?? ''), 200, '...') }}
                                        </p>
                                        <a href="{{route('article.view', ['id' => $article->id, 'slug' => $article->slug])}}" class="service-link">
                                            {{ __('article.read_more') }}
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <!-- End Article Card -->

                        @endif
                    @endforeach
                </div>
            </div>



            {{ $articles->links() }}
        @endif
    </section>

@endsection
