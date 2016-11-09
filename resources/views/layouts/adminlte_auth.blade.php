<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title') - SpotLite</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/main.css')}}">
    <style>
        body, h1, h2, h3, a, .main-header .header-label, div {
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
</head>

<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="{{route('dashboard.index')}}">
            <img src="{{asset('images/logo.png')}}" alt="" width="250">
        </a>
    </div>
    @yield('content')
</div>
<!-- /.login-box -->

@include('scripts.variable_setter')
<script type="text/javascript" src="{{asset('js/main.js')}}"></script>
@yield('scripts')
</body>
</html>
