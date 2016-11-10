@extends('layouts.adminlte')
@section('title', 'Subscription')
@section('header_title', "Change My Plan")
@section('breadcrumbs')
    {!! Breadcrumbs::render('subscription_edit', $subscription) !!}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            {{--<div class="box box-solid">--}}
                {{--<div class="box-body">--}}
                    <div class="row m-b-10">
                        <div class="col-sm-12 text-center">
                            @include('subscriptions.partials.products_copy')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            {!! Form::model($subscription ,array('route' => array('subscription.update', $subscription->getKey()), 'method' => 'put', "id" => "frm-subscription-update", "onsubmit"=>"return false;")) !!}
                            <input type="hidden" name="api_product_id" id="txt-api-product-id">
                            <input type="hidden" name="coupon_code" id="txt-coupon-code">
                            {!! Form::submit('Change My Plan', ["href" => "#", "class"=>"btn btn-primary btn-lg",
                            "id" => "btn-subscribe", "disabled" => "disabled", "onclick"=>"submitSubscriptionUpdateOnclick();"]) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(function () {
            $(".product-container").on("click", function () {
                $(".product-container.selected").removeClass("selected");
                $(this).addClass("selected");
                var productId = $(".product-container.selected").attr("data-id");
                $("#txt-api-product-id").val(productId);
                updateSubscribeButton();
            });
        });

        function updateSubscribeButton() {
            $("#btn-subscribe").prop("disabled", $(".product-container.selected").length == 0 || $(".product-container.selected").hasClass("chosen"));
        }

        function submitSubscriptionUpdateOnclick() {
            var fromPrice = $(".product-container.chosen").attr("data-price");
            var toPrice = $(".product-container.selected").attr("data-price");
            var title, content;
            if (parseInt(fromPrice) > parseInt(toPrice)) {
                title = "Downgrade Subscription";
                content = "By downgrading your subscription you will receive a credit for the pro-rata amount for the rest of the month at the next subscription fee. This credit will be offset against future subscription charges.";
            } else {
                title = "Upgrade Subscription";
                content = "By upgrading your subscription you will be immediately charged the pro-rata amount for the rest of the month at the new subscription fee."
            }
            confirmP(title, content + "<br><br>Are you sure you want to change your subscription?", {
                "affirmative": {
                    "class": "btn-primary",
                    "callback": function () {
                        $("#txt-coupon-code").val($("#visual-coupon-code").val());
                        showLoading();
                        submitSubscriptionUpdate(function (response) {
                            hideLoading();
                            if (response.status == true) {
                                alertP("Updated", "Your subscription plan has been updated.");
                                $(".product-container.chosen").removeClass("chosen");
                                $(".product-container.selected").removeClass("selected");
                                $(".product-container").filter(function () {
                                    return $(this).attr("data-id") == response.subscription.api_product_id;
                                }).addClass("chosen");
                                updateSubscribeButton();
                            } else {
                                console.info('response', response);
                                var errors = "";
                                $.each(response.errors, function (index, error) {
                                    errors += error + " ";
                                });
                                alertP("Error", errors);
                            }

                        }, function (xhr, status, error) {
                            hideLoading();
                        })
                    },
                    "dismiss": true
                },
                "negative": {
                    "class": "btn-default",
                    "dismiss": true
                }
            });
        }

        function submitSubscriptionUpdate(successCallback, errorCallback) {
            $.ajax({
                "url": $("#frm-subscription-update").attr("action"),
                "method": "put",
                "data": $("#frm-subscription-update").serialize(),
                "dataType": "json",
                "success": successCallback,
                "error": errorCallback
            })
        }
    </script>
@stop