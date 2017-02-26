@extends('layouts.adminlte')
@section('title', 'Subscription')
@section('header_title', "Complete your signup process")
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
                    {!! Form::open(array('route' => 'subscription.confirm', 'id' => 'frm-subscribe', 'method' => 'get', 'onsubmit'=>'$("#txt-coupon-code").val($("#visual-coupon-code").val());showLoading()')) !!}
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
        function subscribeNowOnClick(el) {
            var productId = $(el).closest(".plan").attr("data-id");
            $("#txt-api-product-id").val(productId);
            $("#frm-subscribe").submit();
        }
    </script>
@stop