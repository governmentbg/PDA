<!-- upper header section -->
<div class="container-fluid container-xl position-relative d-flex">

    <div class="row">
        <div class="col" style="max-width: 15%">
            <img style="max-width: 90%" src="{{asset('img/13543_MinCultjpg-2.png')}}" alt="">
        </div>
        <div class="col text-center mt-3">
            <h1>
                {{__('general.site_name')}}
            </h1>
        </div>
    </div>


</div>
<!-- upper header section -->

<header id="header" class="header d-flex align-items-center">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

{{--        <a href="{{route('home')}}" class="logo d-flex align-items-center">--}}
{{--            <img src="{{asset('img/13543_MinCultjpg-2.png')}}" alt="">--}}
{{--            <h1 class="sitename">{{__('general.site_name')}}</h1>--}}
{{--        </a>--}}

       @include('partials.navbar')

    </div>
</header>
