<!DOCTYPE html>
<html>
<head>
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
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title') - SpotLite</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
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

    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', '{{config('google_analytics.ua_code')}}', 'auto');
        ga('send', 'pageview');
    </script>
    <script type="text/javascript" src="{{elixir('js/zendesk.js')}}"></script>
    <script type="text/javascript" src="{{elixir('js/main.js')}}"></script>
    <script type="text/javascript" src="{{elixir('js/dashboard.js')}}"></script>
</head>
<body class="hold-transition skin-black-light layout-top-nav">
<!-- Site wrapper -->
<div class="wrapper">

    @include('layout_partials.default_header')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

@include('scripts.variable_setter')

@yield('scripts')

@include('scripts.notification')

</body>
</html>