@extends('layouts.adminlte')
@section('title', 'Register')

@section('content')
    <div class="row">
        {{--<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-sm-12">--}}
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Subscription Plans</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            @include('subscriptions.partials.products')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Register</h3>
                </div>
                <div class="box-body">
                    <div class="um-form-container">
                        @if(isset($errors))
                            <ul class="text-danger">
                                @foreach ($errors->all('<li>:message</li>') as $message)
                                    {!! $message !!}
                                @endforeach
                            </ul>
                        @endif
                        {!! Form::open(array('route' => 'register.post', 'method' => 'post', "id" => "frm-register", "onsubmit" => "$('#txt-coupon-code').val($('#visual-coupon-code').val())")) !!}
                        @include('auth.forms.register_form')
                        <input type="hidden" name="signup_link" id="txt-signup-link">
                        <input type="hidden" name="api_product_id" id="txt-api-product-id">
                        <input type="hidden" name="coupon_code" id="txt-coupon-code">
                        <div class="row m-b-5">
                            <div class="col-sm-6">
                                <a href="{{route('login.get')}}">Already have an account? Click here to login</a>
                            </div>
                            <div class="col-sm-6 text-right">
                                {!! Form::submit('Register', ["class"=>"btn btn-default btn-sm", "disabled" => "disabled", "id" => "btn-register"]) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(function () {
            $(".product-container").on("click", function () {
                $(".product-container.selected").removeClass("selected");
                $(this).addClass("selected");
                var link = $(this).attr("data-link");
                var apiProductID = $(this).attr("data-id");
                $("#txt-signup-link").val(link);
                $("#txt-api-product-id").val(apiProductID);
                updateBtnRegisterStatus();
            });
        });

        function updateBtnRegisterStatus() {
            $("#btn-register").prop("disabled", $(".product-container.selected").length == 0);
        }
    </script>
@stop