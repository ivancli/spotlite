@extends('layouts.adminlte_auth')
@section('title', 'Account Login')
@section('content')

    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        @if(isset($errors))
            <ul class="text-danger">
                @foreach ($errors->all('<li>:message</li>') as $message)
                    {!! $message !!}
                @endforeach
            </ul>
        @endif
        {!! Form::open(array('route' => 'login.post', 'method' => 'post', "id" => "frm-login", "onsubmit" => "gaLogin();")) !!}
        @include('auth.forms.login_form')
        {!! Form::close() !!}

        <div class="social-auth-links text-center">
            <p>- OR -</p>
        </div>
        <a href="{{route('password.get')}}" onclick="gaForgotPassword();">I forgot my password</a><br>
        <a href="{{route('register.get')}}" onclick="gaRegister();" class="text-center">Register a new membership</a>

    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(function(){
            $(".icheck").iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        })
    </script>
@stop