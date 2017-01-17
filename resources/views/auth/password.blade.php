@extends('layouts.adminlte_auth')
@section('title', 'Account Login')

@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{route('dashboard.index')}}">
                <img src="{{asset('build/images/logo_transparent_white_text.png')}}" alt="" width="360">
            </a>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Enter your email to reset password</p>
            @if(isset($errors))
                <ul class="text-danger">
                    @foreach ($errors->all('<li>:message</li>') as $message)
                        {!! $message !!}
                    @endforeach
                </ul>
            @endif
            <ul class="text-danger errors-container">
            </ul>
            {!! Form::open(array('route' => 'password.post', 'method' => 'post', "id" => "frm-password", 'onsubmit' => 'submitForgotPassword(); return false;')) !!}

            <div class="form-group has-feedback">
                {!! Form::email('email', null, array('class' => 'form-control', 'placeholder' => 'Email', 'autocomplete' => 'off')) !!}
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="row m-b-20">
                <div class="col-sm-12">
                    <div class="g-recaptcha" data-sitekey="{{config('google_captcha.site_key')}}"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-push-6 text-right">
                    {!! Form::submit('FORGOT', ["class"=>"btn btn-default btn-block btn-flat", "href"=>"#"]) !!}
                </div>
                <div class="col-sm-6 col-sm-pull-6">
                    <div style="padding-top: 5px; padding-bottom: 5px;">
                        <a href="{{route('login.get')}}">Back to login page</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('scripts')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script type="text/javascript">
        $(function () {
            $(".icheck").iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        })
        function submitForgotPassword() {
            showLoading();
            $.ajax({
                "url": $("#frm-password").attr("action"),
                "method": "post",
                "data": $("#frm-password").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        alertP('Email Sent', 'An email with the reset password link has been sent to the email address provided', function () {
                            window.location.href = "{{route('login.get')}}";
                        });
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    grecaptcha.reset();
                    if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                        var $errorContainer = $(".errors-container");
                        $errorContainer.empty();
                        $.each(xhr.responseJSON, function (key, error) {
                            $.each(error, function (index, message) {
                                $errorContainer.append(
                                        $("<li>").text(message)
                                );
                            })
                        });
                    } else {
                        describeServerRespondedError(xhr.status);
                    }
                }
            })
        }
    </script>
@stop