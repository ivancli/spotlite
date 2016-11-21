<!doctype html>
{{--redirect if cookie is available--}}
<script type="text/javascript">
    if (navigator.cookieEnabled == true) {
        window.location = "{{route('dashboard.index')}}";
    }
</script>
<title>Cookie Disabled</title>
<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<link rel="shortcut icon" type="image/x-icon" href="{{asset('build/images/favicon.ico')}}"/>
{{--<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">--}}
<style>
    body, h1, h2, h3, a, .main-header .header-label, div {
        font-family: 'Lato', sans-serif;
    }
</style>
<style>
    body {
        text-align: center;
        padding: 150px;
    }

    h1 {
        font-size: 35px;
    }

    body {
        font-size: 20px;
        color: #333;
    }

    article {
        display: block;
        text-align: left;
        width: 650px;
        margin: 0 auto;
    }

    a {
        color: #dc8100;
        text-decoration: none;
    }

    a:hover {
        color: #333;
        text-decoration: none;
    }

    .logo {
        text-align: center;
    }
</style>

<article>
    <div class="logo">
        <a href="{{route('dashboard.index')}}">
            <img src="{{asset("build/images/logo.png")}}" alt="" width="400">
        </a>
    </div>
    <h1>Cookie Disabled</h1>
    <div>
        <p>
            Oops! It appears that Cookie is disabled in your browser.
        </p>
        <p>
            Please enable Cookie in order to take advantage of full functionality of SpotLite application.
        </p>
        <p>&mdash; SpotLite Admin</p>
    </div>
</article>