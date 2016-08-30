@extends('layouts.adminlte')
@section('title', 'Subscription')
@section('header_title', "Complete your signup process")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Subscription Plans</h3>
                </div>
                <div class="box-body">
                    <div class="row m-b-5">
                        <div class="col-sm-12 text-center">
                            @foreach($APIProducts as $item)
                                @if(Auth::user()->subscriptions->count() == 0 || $item->product->price_in_cents > 0)
                                    <div class="product-container {{in_array($item->product->id, $chosenAPIProductIDs) ? 'chosen': ''}}"
                                         data-link="{{$item->product->public_signup_pages[0]->url}}"
                                         data-id="{{$item->product->id}}">
                                        <div class="text-center">
                                            <img src="http://placehold.it/150x100" alt="">
                                        </div>
                                        {{--product_id: {{$item->product->id}}--}}
                                        <h4>{{$item->product->name}}</h4>
                                        <p>{{$item->product->description}}</p>
                                        <h4 class="text-center">Price:
                                            ${{number_format($item->product->price_in_cents/100, 2)}}
                                        </h4>
                                        {{--                                    expiration_interval: {{$item->product->expiration_interval}}--}}
                                        {{--expiration_interval_unit: {{$item->product->expiration_interval_unit}}--}}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            {!! Form::open(array('route' => 'subscribe.store', 'method' => 'post')) !!}
                            <input type="hidden" name="api_product_id" id="txt-api-product-id">
                            {!! Form::submit('Subscribe Now', ["class"=>"btn btn-primary btn-lg", "id" => "btn-subscribe", "disabled" => "disabled"]) !!}
                            {!! Form::close() !!}
                        </div>
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
                var productId = $(".product-container.selected").attr("data-id");
                $("#txt-api-product-id").val(productId);
                updateSubscribeButton();
            });
        });

        function updateSubscribeButton() {
            $("#btn-subscribe").prop("disabled", $(".product-container.selected").length == 0);
        }
    </script>
@stop