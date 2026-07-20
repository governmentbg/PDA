<footer id="footer" class="footer position-relative light-background">

    <div class="container">
        <div class="row gy-5">

            <div class="col-lg-4">
                <div class="footer-content">
                    <a href="index.html" class="logo d-flex align-items-center mb-4">
                        <span class="sitename">{{ __('footer.ministry_of_culture') }}</span>
                    </a>
                    <p class="mb-4">{{ __('footer.eu_support') }}</p>

                    <div class="newsletter-form align-items-center">
                        <div class="row">
                            <div class="col">
                                <img style="max-height:90px;" src="{{asset('img/EN_fundedbyEU_VERTICAL_RGB_POS.png')}}" alt="">
                            </div>
                            <div class="col">
                                <img style="max-height:90px;margin-left:15px;" src="{{asset('/img/plan-logo.png')}}" alt="">
                            </div>
                            <div class="col">
                                <img style="max-height:90px;" src="{{asset('/img/13543_MinCultjpg-2.png')}}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $userGuideId = app()->getLocale() === \App\Enums\SettingEnum::LOCALE_BG ? \App\Enums\PageEnum::USER_GUIDE_BG : \App\Enums\PageEnum::USER_GUIDE_EN;
                $aboutUsId   = app()->getLocale() === \App\Enums\SettingEnum::LOCALE_BG ? \App\Enums\PageEnum::FOR_US_BG : \App\Enums\PageEnum::FOR_US_EN;

                $userGuide = \App\Models\Page::find($userGuideId);
                $aboutUs   = \App\Models\Page::find($aboutUsId);

                $faqId       = app()->getLocale() === \App\Enums\SettingEnum::LOCALE_BG ? \App\Enums\PageEnum::FAQ_BG : \App\Enums\PageEnum::FAQ_EN;
                $faqPage   = \App\Models\Page::find($faqId);
            @endphp
            @php

            @endphp
            <div class="col-lg-2 col-6">
                <div class="footer-links">
                    <h4>{{ __('footer.website_owner') }}</h4>
                    <ul>
                        @if($aboutUs)
                            <li>
                                <a href="{{ route('page.show', $aboutUs->sef_title) }}">
                                    <i class="bi bi-chevron-right"></i>{{ $aboutUs->title }}
                                </a>
                            </li>
                        @endif
                        <li><a href="#" id="rss-copy-link"><i class="bi bi-chevron-right"></i>{{ __('footer.rss') }}</a></li>
                        <li><a href="{{route('auth.register')}}"><i class="bi bi-chevron-right"></i>{{ __('footer.registration') }}</a></li>
                            @if($userGuide)
                                <li>
                                    <a href="{{ route('page.show', $userGuide->sef_title) }}">
                                        <i class="bi bi-chevron-right"></i>{{ $userGuide->title }}
                                    </a>
                                </li>
                            @endif
                    </ul>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <div class="footer-links">
                    <h4>{{ __('footer.useful_links') }}</h4>
                    <ul>
                        <li><a href="{{route('article.index')}}"><i class="bi bi-chevron-right"></i>{{ __('footer.news') }}</a></li>
                        <li><a href="{{route('cultural_object.index')}}"><i class="bi bi-chevron-right"></i>{{ __('footer.cultural_objects') }}</a></li>
                        <li><a href="{{route('provider.index')}}"><i class="bi bi-chevron-right"></i>{{ __('footer.providers') }}</a></li>
                        @if($userGuide)
                            <li>
                        <a href="{{ $faqPage ? route('page.show', $faqPage->sef_title) : '#' }}">
                            <i class="bi bi-chevron-right"></i>
                            {{ __('general.home.faq') }}
                        </a>
                            @endif
                    </ul>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="footer-contact">
                    <h4>{{ __('footer.contact_us') }}</h4>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="contact-info">
                            <p>{{ __('footer.address_blvd') }}<br>{{ __('footer.address_city') }}</p>
                        </div>
                    </div>

                    <div class="contact-item d-flex align-items-center">
                        <div class="contact-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="contact-info">
                            <p>{{ __('footer.contact_phone') }}</p>
                        </div>
                    </div>

                    <div class="contact-item d-flex align-items-center">
                        <div class="contact-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="contact-info">
                            <p>{{ __('footer.contact_email') }}</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="copyright">
                        <p><strong class="px-1 sitename">{{ __('footer.ministry_of_culture') }}</strong> </p>
                        <p class="px-1">
                            {{--Matomo counter--}}
                            <x-visit-counter />
                            {{--Matomo counter--}}
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="footer-bottom-links">
{{--                        <a href="#">Политика на поверителност</a>--}}
                        <a href="#">{{ __('footer.terms') }}</a>
{{--                        <a href="#">Карта на сайта</a>--}}
                    </div>
                    <div class="credits">
                        <!-- insert credits link here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>


<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Preloader -->
<div id="preloader"></div>
<style>
    .footer-bottom {
        padding-bottom: 60px !important;
        position: relative;
    }
</style>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const error = @json(session('swal_error'));

            if (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error,
                });
            }

        });
    </script>
@endpush
