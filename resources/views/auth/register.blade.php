@extends('layouts.adminlte_auth')
@section('title', 'Account Registration')
@section('content')
    <style>
        .register-box {
            width: 1200px;
            max-width: 100%;
        }

        .register-box-body {
            width: 360px;
            margin: 0 auto;
            display: none;
        }
    </style>
    <div class="register-box">
        <div class="register-logo">
            <a href="{{route('dashboard.index')}}">
                <img src="{{asset('images/logo.png')}}" alt="" width="250">
            </a>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @include('subscriptions.partials.products_copy')
            </div>
        </div>
        <div class="register-box-body">
            <div class="registration-form">
                <p class="register-box-msg">Register a new membership</p>
                @if(isset($errors))
                    <ul class="text-danger">
                        @foreach ($errors->all('<li>:message</li>') as $message)
                            {!! $message !!}
                        @endforeach
                    </ul>
                @endif

                {!! Form::open(array('route' => 'register.post', 'method' => 'post', "id" => "frm-register", "onsubmit" => "$('#txt-coupon-code').val($('#visual-coupon-code').val())")) !!}
                @include('auth.forms.register_form')
                <input type="hidden" name="signup_link" id="txt-signup-link" value="{{old("signup_link")}}">
                <input type="hidden" name="api_product_id" id="txt-api-product-id" value="{{old("api_product_id")}}">
                <input type="hidden" name="coupon_code" id="txt-coupon-code" value="{{old("coupon_code")}}">
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox"> &nbsp; I agree to the <a href="#">terms</a>
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        {!! Form::submit('Register', ["class"=>"btn btn-primary btn-block btn-flat", "disabled" => "disabled", "id" => "btn-register"]) !!}
                    </div>
                </div>
                {!! Form::close() !!}

                <a href="{{route('login.get')}}" class="text-center">I already have a membership</a>
            </div>
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
            $(".product-container").on("click", function () {
                $(".product-container.selected").removeClass("selected");
                $(this).addClass("selected");
                var link = $(this).attr("data-link");
                var apiProductID = $(this).attr("data-id");
                $("#txt-signup-link").val(link);
                $("#txt-api-product-id").val(apiProductID);
                updateRegistrationPanelStatus();
                updateBtnRegisterStatus();
            });
            updateRegistrationPanelStatus();
            updateBtnRegisterStatus();
        });

        function updateBtnRegisterStatus() {
            $("#btn-register").prop("disabled", $(".product-container.selected").length == 0);
        }

        function updateRegistrationPanelStatus() {
            if ($(".product-container.selected").length == 0) {
                $(".register-box-body").slideUp();
            } else {
                $(".register-box-body").slideDown();
                $('html, body').animate({
                    scrollTop: $(".register-box-body").offset().top
                }, 500);
            }
        }
    </script>
@stop

