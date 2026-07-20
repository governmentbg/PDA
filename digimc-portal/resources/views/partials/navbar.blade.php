<nav id="navmenu" class="navmenu">
    @php
        $aboutUsId   = app()->getLocale() === \App\Enums\SettingEnum::LOCALE_BG ? \App\Enums\PageEnum::FOR_US_BG : \App\Enums\PageEnum::FOR_US_EN;
        $aboutUs   = \App\Models\Page::find($aboutUsId);
    @endphp
    <ul>
        <li><a class="{{Route::is('home')? 'active' : ''}}" href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
        <li><a class="{{Route::is('article.*')? 'active' : ''}}" href="{{ route('article.index') }}">{{ __('navbar.articles') }}</a></li>
        <li><a class="{{Route::is('provider.*')? 'active' : ''}}" href="{{ route('provider.index') }}">{{ __('navbar.providers') }}</a></li>
        <li><a class="{{Route::is('cultural_object.*')? 'active' : ''}}" href="{{ route('cultural_object.index') }}">{{ __('navbar.cultural_object') }}</a></li>
        @if($aboutUs)
            <li>
                <a class="{{Route::is('page.*')? 'active' : ''}}" href="{{ route('page.show', $aboutUs->sef_title) }}">
                    {{ $aboutUs->title }}
                </a>
            </li>
        @endif
        <li><a class="{{Route::is('gallery.*')? 'active' : ''}}" href="{{ route('gallery.index') }}">{{ __('profile.collection') }}</a></li>

        @if(!\Auth::check())
            <li class="dropdown"><a href="#"><span>{{ __('navbar.login_registration') }}</span> <i
                        class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                    <li><a href="{{ route('auth.login') }}">{{ __('navbar.login') }}</a></li>
                    <li><a href="{{ route('auth.register') }}">{{ __('navbar.register') }}</a></li>
                    <li><a href="{{ route('auth.password.request') }}">{{ __('navbar.forgot_password') }}</a></li>
                </ul>
            </li>
        @else
            @role(\App\Models\Role::ADMINISTRATOR)
            <li class="dropdown"><a href="#"><span>Администрация</span> <i
                        class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                    <li><a href="{{ route('manage.settings.index') }}">Настройки</a></li>
                    <li class="dropdown"><a href="#"><span>Новини</span> <i
                                class="bi bi-chevron-down toggle-dropdown"></i></a>
                        <ul>
                            <li><a href="{{ route('manage.article_type.index') }}">Типове</a></li>
                            <li><a href="{{ route('manage.article.index') }}">Новини</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('manage.page.index') }}">Страници</a></li>
                    <li><a href="{{route('manage.gallery.index')}}">{{ __('profile.collection') }}</a></li>
{{--                    <li><a href="{{ route('manage.payments.index') }}">Плащания</a></li>--}}
                </ul>
            </li>
            @endrole

            <li class="dropdown"><a href="#"><span>{{ __('profile.profile') }}</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                    <li><a href="{{ route('profile.edit') }}"> {{ __('profile.info') }} </a></li>
{{--                    <li><a href="{{ route('profile.cart.index') }}"> {{ __('cart.my_cart') }} </a></li>--}}
{{--                    <li><a href="{{ route('profile.payments.index') }}"> {{ __('payments.title') }} </a></li>--}}
                    <li><a href="{{ route('profile.password.edit') }}"> {{ __('profile.security') }} </a></li>
                    <li><a href="{{ route('profile.favorites.index') }}"> {{ __('profile.favorite') }} </a></li>
                    <li><a href="{{ route('profile.galleries.index') }}"> {{ __('navbar.collections') }} </a></li>
                </ul>
            <li><a href="{{ route('auth.logout') }}" onclick="return confirmLogout(this)">{{ __('navbar.logout') }}</a></li
        @endif
        <div class="search-bar-wrapper">
            @livewire('search-bar')
        </div>
    </ul>
    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
</nav>

<div id="rss-copied-msg" style="
    display: none;
    position: fixed;
    top: 20px;
    right: 20px;
    background: #4caf50;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    z-index: 9999;
    font-size: 14px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
">
    {{__('general.rss_link_copied')}}
</div>

@push('scripts')
    <script>
        function confirmLogout(element) {
            Swal.fire({
                title: '{{ __("general.auth.logout_title") }}',
                text: '{{ __("general.auth.logout_message") }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("general.auth.logout_confirm") }}',
                cancelButtonText: '{{ __("general.auth.logout_cancel") }}',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = element.href;
                }
            });

            return false;
        }
        $(document).ready(function() {
            $('#rss-copy-link').click(function(e) {
                e.preventDefault();
                var rssUrl = "{{ url('/feed') }}";

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(rssUrl)
                        .then(function() {
                            showCopiedMessage();
                        })
                        .catch(function(err) {
                            console.error('Грешка при копиране:', err);
                        });
                } else {
                    var $temp = $("<input>");
                    $("body").append($temp);
                    $temp.val(rssUrl).select();
                    document.execCommand("copy");
                    $temp.remove();
                    showCopiedMessage();
                }
            });

            function showCopiedMessage() {
                var $msg = $('#rss-copied-msg');
                $msg.fadeIn(200).delay(1500).fadeOut(400);
            }
        });
    </script>
@endpush

