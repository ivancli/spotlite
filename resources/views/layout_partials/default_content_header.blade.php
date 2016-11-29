{{--<section class="content-header">--}}
    {{--<h1>--}}
        {{--@yield('header_title')--}}
    {{--</h1>--}}
    {{--@yield('breadcrumbs')--}}
{{--</section>--}}
<section class="content-header">
    <div class="row">
        <div class="col-sm-6">
            <h2>
                @yield('header_title')
            </h2>
        </div>
        <div class="col-sm-6">
            @yield('breadcrumbs')
        </div>
    </div>
</section>