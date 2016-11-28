{{--<section class="content-header">--}}
{{--<h1>--}}
{{--@yield('header_title')--}}
{{--</h1>--}}
{{--@yield('breadcrumbs')--}}
{{--</section>--}}
<section class="content-header">
    <style>
        .general-search-input {
            border-radius: 20px;
            height: 45px;
            padding-left: 35px;
            padding-right: 35px;
        }

        .general-search-button {
            position: absolute;
            right: 15px;
            top: 0;
            height: 45px;
            width: 70px;
            border-radius: 25px;
        }
    </style>
    <div class="row">
        <div class="col-sm-6">
            <h2>
                @yield('header_title')
            </h2>
        </div>
        <div class="col-sm-6">
            <input type="text" class="form-control general-search-input" placeholder="Search">
            <button class="btn btn-default general-search-button">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</section>