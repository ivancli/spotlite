<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - SpotLite</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('images/favicon.ico')}}"/>
    {{--<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{asset('css/main.css')}}">
    <style>
        body, h1, h2, h3, a, .main-header .header-label, div{
            font-family: 'Lato', sans-serif;
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
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', '{{config('google_analytics.ua_code')}}', 'auto');
        ga('send', 'pageview');
    </script>
    <script type="text/javascript" src="{{asset('js/zendesk.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/main.js')}}"></script>
</head>
<body class="hold-transition skin-black-light sidebar-mini">
<div class="wrapper">

@include('layout_partials.default_header')

<!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
@include('layout_partials.default_sidebar')

<!-- =============================================== -->

    @if(Auth::check() && Auth::user()->last_login)

    @endif
    <div class="content-wrapper">
        @yield('notification_banner')
        @include('layout_partials.default_content_header')

        <section class="content">
            @yield('content')
        </section>
    </div>
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

@yield('scripts')

@include('scripts.notification')

</body>
</html>

