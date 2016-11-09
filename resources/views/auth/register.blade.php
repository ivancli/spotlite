@extends('layouts.adminlte_auth')
@section('title', 'Account Registration')
@section('content')
    <style>
        .register-box {
            width: 1200px;
            max-width: 100%;
            margin-top: 20px;
        }

        .register-box-body {
            width: 360px;
            margin: 0 auto;
            display: none;
            max-width: 100%;
        }

        .register-box-body ul.text-danger {
            padding-left: 20px;
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
                <p class="register-box-msg">Sign up now</p>
                @if(isset($errors))
                    <ul class="text-danger">
                        @foreach ($errors->all('<li>:message</li>') as $message)
                            {!! $message !!}
                        @endforeach
                    </ul>
                @endif

                {!! Form::open(array('route' => 'register.post', 'method' => 'post', "id" => "frm-register", "onsubmit" => "return validateRegistrationForm();")) !!}
                @include('auth.forms.register_form')
                <input type="hidden" name="signup_link" id="txt-signup-link" value="{{old("signup_link")}}">
                <input type="hidden" name="api_product_id" id="txt-api-product-id" value="{{old("api_product_id")}}">
                <div class="row">
                    <div class="col-sm-7">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" value="y" name="agree_terms" id="chk-agree-terms"> &nbsp; I agree
                                to the <a href="#">terms</a>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        {!! Form::submit('Sign up now', ["class"=>"btn btn-primary btn-block btn-flat", "disabled" => "disabled", "id" => "btn-register"]) !!}
                    </div>
                </div>
                {!! Form::close() !!}

                <a href="{{route('login.get')}}" class="text-center">I already have a subscription</a>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(function () {
            $(".coupon-code-container").remove();
            $(".icheck").iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
            $(".btn-select").on("click", function () {
                /* button */
                $(".btn-select").text("Select").prop("disabled", false);
                $(".plan.selected").removeClass("selected");
                $(this).text("Selected").prop("disabled", true);
                var $plan = $(this).closest(".plan");
                $plan.addClass("selected");
                var link = $plan.attr("data-link");
                var apiProductID = $plan.attr("data-id");
                $("#txt-signup-link").val(link);
                $("#txt-api-product-id").val(apiProductID);
                updateRegistrationPanelStatus();
                updateBtnRegisterStatus();
                return false;
            });
            updateRegistrationPanelStatus();
            updateBtnRegisterStatus();
        });

        function updateBtnRegisterStatus() {
            $("#btn-register").prop("disabled", $(".plan.selected").length == 0);
        }

        function updateRegistrationPanelStatus() {
            if ($(".plan.selected").length == 0) {
                $(".register-box-body").slideUp();
            } else {
                $(".register-box-body").slideDown();
                $('html, body').animate({
                    scrollTop: $(".register-box-body").offset().top
                }, 500);
            }
        }

        function validateRegistrationForm() {
            $("ul.text-danger").empty();
            var isValid = true;
            $("#frm-register").find(".form-group.required").each(function () {
                $(this).find("input[type=text],input[type=password],input[type=email]").each(function () {
                    if ($(this).val() == "") {
                        isValid = false;
                        var errorMsg = $(this).attr("placeholder") + " is required.";
                        $("ul.text-danger").append(
                                $("<li>").text(errorMsg)
                        )
                    }
                });
                $(this).find("select").each(function () {
                    if ($(this).val() == "") {
                        isValid = false;
                        var errorMsg = $(this).find("option[value='']").text() + " is required";
                        $("ul.text-danger").append(
                                $("<li>").text(errorMsg)
                        )
                    }
                });
            });
            if (!$("#chk-agree-terms").is(":checked")) {
                isValid = false;
                $("ul.text-danger").append(
                        $("<li>").text("You need to agree with our terms in order to sign up")
                )
            }
            return isValid;
        }
    </script>
@stop

