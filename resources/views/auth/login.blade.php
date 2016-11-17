@extends('layouts.adminlte_auth')
@section('title', 'Account Login')
@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{route('dashboard.index')}}">
                <img src="{{asset('build/images/logo.png')}}" alt="" width="250">
            </a>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>
            @if(isset($errors))
                <ul class="text-danger">
                    @foreach ($errors->all('<li>:message</li>') as $message)
                        {!! $message !!}
                    @endforeach
                </ul>
            @endif
            {!! Form::open(array('route' => 'login.post', 'method' => 'post', "id" => "frm-login", "onsubmit" => "gaLogin();showLoading();")) !!}
            @include('auth.forms.login_form')
            {!! Form::close() !!}
            <div style="margin-bottom: 10px;"></div>
            <a href="{{route('password.get')}}" onclick="gaForgotPassword();">I forgot my password</a><br>
            <a href="{{route('register.get')}}" onclick="gaRegister();" class="text-center">New to SpotLite? Sign up now!</a>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(function () {
            $(".icheck").iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        })
    </script>
@stop