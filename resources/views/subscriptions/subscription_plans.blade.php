@extends('layouts.adminlte')
@section('title', 'Subscription')
@section('header_title', "Complete your signup process")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row m-b-10">
                <div class="col-sm-12 text-center">
                    @include('subscriptions.partials.products_copy')
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-center">
                    {!! Form::open(array('route' => 'subscription.store', 'id' => 'frm-subscribe', 'method' => 'post', 'onsubmit'=>'$("#txt-coupon-code").val($("#visual-coupon-code").val());showLoading()')) !!}
                    <input type="hidden" name="api_product_id" id="txt-api-product-id">
                    <input type="hidden" name="coupon_code" id="txt-coupon-code">
                    {{--                    {!! Form::submit('Subscribe Now', ["class"=>"btn btn-primary btn-lg", "id" => "btn-subscribe", "disabled" => "disabled"]) !!}--}}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(function () {
//            $(".product-container").on("click", function () {
//                $(".product-container.selected").removeClass("selected");
//                $(this).addClass("selected");
//                var productId = $(".product-container.selected").attr("data-id");
//                $("#txt-api-product-id").val(productId);
//                updateSubscribeButton();
//            });
        });

        function subscribeNowOnClick(el) {
            var productId = $(el).closest(".plan").attr("data-id");
            $("#txt-api-product-id").val(productId);
            $("#frm-subscribe").submit();
        }

        //        function updateSubscribeButton() {
        //            $("#btn-subscribe").prop("disabled", $(".product-container.selected").length == 0);
        //        }
    </script>
@stop