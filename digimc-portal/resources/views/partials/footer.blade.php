<!-- jQuery -->
<script src="{{ asset('vendor/jquery/jquery-3.5.1.min.js')}}"></script>

<!-- Vendor JS Files -->
<script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('vendor/glightbox/js/glightbox.min.js')}}"></script>
<script src="{{asset('vendor/swiper/swiper-bundle.min.js')}}"></script>
<script src="{{asset('vendor/purecounter/purecounter_vanilla.js')}}"></script>
<script src="{{asset('vendor/imagesloaded/imagesloaded.pkgd.min.js')}}"></script>
<script src="{{asset('vendor/isotope-layout/isotope.pkgd.min.js')}}"></script>


<!-- Main JS File -->
<script src="{{asset('js/main.js')}}"></script>
<livewire:scripts/>

{{-- Balloons --}}
@include('components.language-bubble')
@include('components.accessibility')
@include('components.feedback-bubble')

<!-- reCAPTCHA API -->
<script src="https://www.google.com/recaptcha/api.js?render=explicit&hl={{ session('locale', 'en') }}" async defer></script>

@stack('scripts')

@include('sweetalert::alert')
