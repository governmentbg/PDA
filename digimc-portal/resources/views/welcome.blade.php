@extends('layouts.app')

@section('content')
    <section id="" class="hero section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1>{{ __('general.home.digital') }} <span>{{ __('general.home.cultural_heritage') }}</span> {{ __('general.home.of_bulgaria') }}</h1>
                        <p>{{ __('footer.eu_support') }}</p>

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="{{asset('img/13543_MinCultjpg-2.png')}}" class="img-fluid floating" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($latestNews->count() > 0)
        <section class="section cultural latest-articles py-5">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2>{{ __('general.home.latest_news_title') }}</h2>
                    <p>{{ __('general.home.latest_news_subtitle') }}</p>
                </div>

                <div class="row g-4">
                    @foreach($latestNews as $article)
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <a href="{{ route('article.view', ['id' => $article->id, 'slug' => $article->slug]) }}" class="article-card text-decoration-none d-block h-100">
                                <div class="image-full-container position-relative overflow-hidden rounded-3 shadow-sm">
                                    @if(!empty($article->image?->url))
                                        <img src="{{ $article->image->url }}" alt="{{ $article->title }}" class="img-fluid w-100 h-100">
                                    @else
                                        <x-thumbnail-placeholder type="default" :title="$article->title" class="w-100 h-100" />
                                    @endif
                                    <div class="content-overlay position-absolute bottom-0 start-0 end-0 text-white p-4">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <h4 class="mb-2 fw-bold flex-grow-1">{{ $article->title }}</h4>
                                            @if(!empty($article->published_at))
                                                <small class="opacity-75 text-nowrap">
                                                    {{ $article->published_at->format('d.m.Y') }}
                                                </small>
                                            @endif
                                        </div>
                                        <p class="mb-1 opacity-90">{{ Str::limit(strip_tags($article->content), 120) }}</p>
                                    </div>
                                    <div class="hover-overlay position-absolute top-0 start-0 end-0 bottom-0 bg-dark opacity-0 transition-all"></div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('article.index') }}" class="btn btn-outline-primary">
                        {{ __('general.home.view_all_news') }}
                    </a>
                </div>
            </div>
        </section>
    @endif

    @if($recentPublicCollections->count() > 0)
        <section class="section cultural latest-collections py-5">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2>{{ __('general.home.latest_collections_title') }}</h2>
                    <p>{{ __('general.home.latest_collections_subtitle') }}</p>
                </div>

                <div class="row g-4">
                    @foreach($recentPublicCollections as $gallery)
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <a href="{{ route('gallery.view', ['gallery' => $gallery->id]) }}" class="collection-card text-decoration-none d-block h-100">
                                <div class="image-full-container position-relative overflow-hidden rounded-3 shadow-sm">
                                    @if(!empty($gallery->preview_thumbnail_url))
                                        <img src="{{ $gallery->preview_thumbnail_url }}" alt="{{ $gallery->name }}" class="img-fluid w-100 h-100">
                                    @else
                                        <x-thumbnail-placeholder
                                            :type="$gallery->preview_placeholder_type ?? 'default'"
                                            :title="$gallery->name"
                                            class="w-100 h-100" />
                                    @endif
                                    <div class="content-overlay position-absolute bottom-0 start-0 end-0 text-white p-4">
                                        <h4 class="mb-2 fw-bold">{{ $gallery->name }}</h4>
                                        @if(!empty($gallery->description))
                                            <p class="mb-1 opacity-90">{{ Str::limit($gallery->description, 120) }}</p>
                                        @endif
                                    </div>
                                    <div class="hover-overlay position-absolute top-0 start-0 end-0 bottom-0 bg-dark opacity-0 transition-all"></div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('gallery.index') }}" class="btn btn-outline-primary">
                        {{ __('general.home.view_all_collections') }}
                    </a>
                </div>
            </div>
        </section>
    @endif

    @if($culturalObjects->count() > 0)
        <section class="section cultural featured-objects py-5">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2>{{ __('general.home.featured_objects_title') }}</h2>
                    <p>{{ __('general.home.featured_objects_subtitle') }}</p>
                </div>
                <div class="row g-4">
                    @foreach($culturalObjects as $item)
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <a href="{{ route('cultural_object.view', ['id' => $item['id']]) }}" class="cultural-object-full text-decoration-none d-block h-100">
                                <div class="image-full-container position-relative overflow-hidden rounded-3 shadow-sm">
                                    @if(!empty($item['thumbnail_url']))
                                        <img src="{{$item['thumbnail_url']}}" alt="{{$item['title']}}" class="img-fluid w-100 h-100">
                                    @else
                                        <x-thumbnail-placeholder
                                            :type="$item['main_web_view_resource']['visualizationtype'] ?? 'default'"
                                            :title="$item['title']"
                                            class="w-100 h-100" />
                                    @endif
                                    <div class="content-overlay position-absolute bottom-0 start-0 end-0 text-white p-4">
                                        <h4 class="mb-2 fw-bold">{{$item['title']}}</h4>
                                        <p class="mb-1 opacity-90">{{ Str::limit($item['description'], 120) }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="badge bg-light text-dark">{{$item['rights_holder']}}</span>
                                            @if(!empty($item['provider']['title']))
                                                <small class="opacity-75">{{$item['provider']['title']}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="hover-overlay position-absolute top-0 start-0 end-0 bottom-0 bg-dark opacity-0 transition-all"></div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('cultural_object.index') }}" class="btn btn-outline-primary">  {{ __('general.home.view_all_objects') }}</a>
                </div>
            </div>
        </section>
    @endif
@endsection

@push('styles')
    <style>
        .cultural-object-full .image-full-container,
        .collection-card .image-full-container,
        .article-card .image-full-container {
            height: 320px;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .cultural-object-full img,
        .collection-card img,
        .article-card img,
        .cultural-object-full .thumbnail-placeholder,
        .collection-card .thumbnail-placeholder,
        .article-card .thumbnail-placeholder {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .cultural-object-full:hover .image-full-container,
        .collection-card:hover .image-full-container,
        .article-card:hover .image-full-container {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .cultural-object-full:hover img,
        .collection-card:hover img,
        .article-card:hover img {
            transform: scale(1.05);
        }

        .content-overlay {
            background: linear-gradient(transparent 0%, rgba(0,0,0,0.9) 100%);
            transition: all 0.3s ease;
        }

        .cultural-object-full:hover .content-overlay,
        .collection-card:hover .content-overlay,
        .article-card:hover .content-overlay {
            background: linear-gradient(transparent 0%, rgba(0,0,0,0.95) 100%);
            padding-bottom: 25px;
        }

        .hover-overlay {
            transition: opacity 0.3s ease;
        }

        .cultural-object-full:hover .hover-overlay,
        .collection-card:hover .hover-overlay,
        .article-card:hover .hover-overlay {
            opacity: 0.1;
        }

        .content-overlay h4 {
            font-size: 1.25rem;
            line-height: 1.3;
        }

        .content-overlay p {
            font-size: 0.95rem;
            line-height: 1.4;
        }


        @media (max-width: 768px) {
            .cultural-object-full .image-full-container,
            .collection-card .image-full-container,
            .article-card .image-full-container {
                height: 280px;
            }
        }

        @media (max-width: 576px) {
            .cultural-object-full .image-full-container,
            .collection-card .image-full-container,
            .article-card .image-full-container {
                height: 250px;
            }

            .content-overlay {
                padding: 15px;
            }

            .content-overlay h4 {
                font-size: 1.1rem;
            }
        }
    </style>
@endpush
