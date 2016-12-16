@extends('layouts.adminlte')
@section('title', 'Subscription')
@section('header_title', "Change My Plan")
@section('breadcrumbs')
{{--    {!! Breadcrumbs::render('subscription_edit', $subscription) !!}--}}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row m-b-10">
                <div class="col-sm-12">
                    @include('subscriptions.partials.products')
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-center">
                    {!! Form::model($subscription ,array('route' => array('subscription.update', $subscription->getKey()), 'method' => 'put', "id" => "frm-subscription-update", "onsubmit"=>"return false;")) !!}
                    <input type="hidden" name="api_product_id" id="txt-api-product-id">
                    <input type="hidden" name="coupon_code" id="txt-coupon-code">
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">


        function submitSubscriptionUpdateOnclick(el) {
            var productId = $(el).closest(".plan").attr("data-id");
            $("#txt-api-product-id").val(productId);
            var fromPrice = $("a.button-blue.disabled").closest(".plan").attr("data-price");
            var toPrice = $(el).closest(".plan").attr("data-price");
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
                    "class": "btn-primary btn-flat",
                    "text": "CONFIRM",
                    "callback": function () {
                        $("#txt-coupon-code").val($("#visual-coupon-code").val());
                        showLoading();
                        submitSubscriptionUpdate(function (response) {
                            hideLoading();
                            if (response.status == true) {
                                alertP("Updated", "Your subscription plan has been updated.");
                                $(".plan").find(".button-blue.disabled").removeClass("disabled").attr("onclick", "submitSubscriptionUpdateOnclick(this);return false;");
                                $(".plan").filter(function () {
                                    return $(this).attr("data-id") == response.subscription.api_product_id;
                                }).find(".button-blue").addClass("disabled").attr("onclick", "return false;");
                                updateButtonText();
                            } else {
                                console.info('response', response);
                                var errors = "";
                                $.each(response.errors, function (index, error) {
                                    errors += error + " ";
                                });
                                alertP("Oops! Something went wrong.", errors);
                            }
                        }, function (xhr, status, error) {
                            hideLoading();
                            describeServerRespondedError(xhr.status);
                        })
                    },
                    "dismiss": true
                },
                "negative": {
                    "class": "btn-default btn-flat",
                    "text": "CANCEL",
                    "dismiss": true
                }
            });
        }

        function updateButtonText() {
            $(".plan").each(function () {
                if ($(this).find(".button-blue").hasClass("disabled")) {
                    $(this).find(".button-blue").text("My Plan");
                }
                else {
                    var buttonText = parseFloat($(this).attr("data-price")) > parseFloat($(".button-blue.disabled").closest(".plan").attr("data-price")) ? "UPGRADE" : "DOWNGRADE";
                    $(this).find(".button-blue").text(buttonText);
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