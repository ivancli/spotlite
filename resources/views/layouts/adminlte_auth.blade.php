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
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title') - SpotLite</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('build/images/favicon.ico')}}"/>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="stylesheet" href="{{elixir('css/main.css')}}">
    <style>
        body, h1, h2, h3, h4, h5, a, .main-header .header-label, div {
            font-family: 'Flama', 'Lato', sans-serif;
        }

        html, body.login-page, body.register-page {
            background-color: #7ed0c0
        }

    </style>

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
    <script type="text/javascript" src="{{elixir('js/auth.js')}}"></script>

@yield('links')

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="hold-transition login-page">
@yield('content')
<!-- /.login-box -->

@include('scripts.variable_setter')
@yield('scripts')
</body>
</html>
