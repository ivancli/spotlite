<!DOCTYPE html>
<html>
<head>
    @if(auth()->check() && auth()->user()->conversion_tracked != 'y')
        @if(auth()->user()->subscription_location == 'us')
            <script type="text/javascript">
                var capterra_vkey = '4596f7f001c14d80b9df45fb40bed681',
                    capterra_vid = '2094080',
                    capterra_prefix = (('https:' == document.location.protocol) ? 'https://ct.capterra.com' : 'http://ct.capterra.com');
                (function () {
                        var ct = document.createElement('script');
                        ct.type = 'text/javascript';
                        ct.async = true;
                        ct.src = capterra_prefix + '/capterra_tracker.js?vid=' + capterra_vid + '&vkey=' + capterra_vkey;
                        var s = document.getElementsByTagName('script')[0];
                        s.parentNode.insertBefore(ct, s);
                    })();
            </script>
        @else
        <!-- Google Code for Sign Up Conversion Page -->
            <script type="text/javascript">
                /* <![CDATA[ */
                var google_conversion_id = 855050554;
                var google_conversion_language = "en";
                var google_conversion_format = "3";
                var google_conversion_color = "ffffff";
                var google_conversion_label = "MSeiCNX9rXAQupLclwM";
                var google_conversion_value = 0.00;
                var google_conversion_currency = "AUD";
                var google_remarketing_only = false;
                /* ]]> */
            </script>
            <script type="text/javascript"
                    src="//www.googleadservices.com/pagead/conversion.js">
            </script>
            <noscript>
                <div style="display:inline;">
                    <img height="1" width="1" style="border-style:none;" alt=""
                         src="//www.googleadservices.com/pagead/conversion/855050554/?value=0.00&amp;currency_code=AUD&amp;label=MSeiCNX9rXAQupLclwM&amp;guid=ON&amp;script=0"/>
                </div>
            </noscript>
        @endif
        <?php auth()->user()->setConversionTracked(); ?>
    @endif
    {{--redirect if js not available--}}
    <noscript>
        <meta http-equiv="refresh" content="0; url={{route('errors.javascript_disabled')}}"/>
    </noscript>
    {{--redirect if cookie not available, unable to store login session anyway without cookie--}}
    <script type="text/javascript">
        if (navigator.cookieEnabled == false) {
            window.location = "{{route('errors.cookie_disabled')}}";
        }
    </script>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - SpotLite</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('build/images/favicon.ico')}}"/>
    {{--<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{elixir('css/main.css')}}">
    <style>
        body, h1, h2, h3, h4, h5, a, .main-header .header-label, div {
            font-family: 'Flama', 'Lato', sans-serif;
        }
    </style>
    @yield('links')

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    @include('scripts.variable_setter')

    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
        ga('create', '{{config('google_analytics.ua_code')}}', 'auto');
        ga('send', 'pageview');
    </script>


    <script type="text/javascript" src="{{elixir('js/zendesk.js')}}"></script>
    <script type="text/javascript" src="{{elixir('js/main.js')}}"></script>
    @yield('head_scripts')
    <script type="text/javascript" src="{{elixir('js/dashboard.js')}}"></script>
    <script type="text/javascript" src="{{elixir('js/spotlite.js')}}"></script>

</head>
<body class="hold-transition skin-black-light layout-top-nav">
<div class="wrapper">

@include('layout_partials.default_header')

<!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
{{--@include('layout_partials.default_sidebar')--}}

<!-- =============================================== -->

    @if(Auth::check() && Auth::user()->last_login)

    @endif
    <div class="content-wrapper">
        <div class="container">
            @yield('notification_banner')
            @include('layout_partials.default_content_header')

            <section class="content">
                @yield('content')
            </section>
        </div>
    </div>
</div>
<!-- ./wrapper -->

@yield('scripts')

@include('scripts.notification')
</body>
</html>

