@extends('layouts.adminlte')
@section('title', 'Subscription')
@section('header_title', "Change My Plan")
@section('breadcrumbs')
    {!! Breadcrumbs::render('subscription_edit', $subscription) !!}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row m-b-10">
                <div class="col-sm-12 text-center">
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
            var fromPrice = $(".plan.chosen").attr("data-price");
            var toPrice = $(".plan.selected").attr("data-price");
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
                    "callback": function () {
                        $("#txt-coupon-code").val($("#visual-coupon-code").val());
                        showLoading();
                        submitSubscriptionUpdate(function (response) {
                            hideLoading();
                            if (response.status == true) {
                                alertP("Updated", "Your subscription plan has been updated.");
                                $(".plan.chosen").removeClass("chosen");
                                $(".plan").filter(function () {
                                    return $(this).attr("data-id") == response.subscription.api_product_id;
                                }).addClass("chosen");
                                updateButtonText();
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
                            describeServerRespondedError(xhr.status);
                        })
                    },
                    "dismiss": true
                },
                "negative": {
                    "class": "btn-default btn-flat",
                    "dismiss": true
                }
            });
        }

        function updateButtonText() {
            $(".plan").each(function () {
                if ($(this).hasClass("chosen")) {
                    $(this).find(".btn-select").text("My Plan").prop("disabled", true);
                }
                else {
                    var buttonText = parseFloat($(this).attr("data-price")) > parseFloat($(".plan.chosen").attr("data-price")) ? "Upgrade" : "Downgrade";
                    $(this).find(".btn-select").text(buttonText).prop("disabled", false);
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