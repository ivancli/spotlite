@if(!is_null($productFamilies))
    @foreach($productFamilies as $productFamily)
        <div class="product-container m-b-10
        {{isset($chosenAPIProductID) && $productFamily->product->id == $chosenAPIProductID ? 'chosen': ''}}
                {{(is_null(old("api_product_id")) && \Request::route()->getName() == "register.get"  && \Request::has("pid") && \Request::get("pid") == $productFamily->product->id) ? 'selected' : ''}}
        {{old("api_product_id") == $productFamily->product->id ? "selected" : ""}}
                "
             data-link="{{array_first($productFamily->product->public_signup_pages)->url}}"
             data-family-id="{{$productFamily->id}}"
             data-id="{{$productFamily->product->id}}"
             style="border: 1px solid lightgrey; border-radius: 20px;"
             data-price="{{$productFamily->product->price_in_cents}}"
             data-component-id="{{$productFamily->component->id}}">
            <h4 style="text-transform: uppercase; color: #78a300;">{{$productFamily->product->name}}</h4>
            <p>
                {!! $productFamily->product->description !!}
            </p>
            @if(!is_null($productFamily->component))
                @foreach($productFamily->component->prices as $price)
                    <p style="color: #78a300;">
                        @if(!is_null($price->ending_quantity))
                            Up to <strong>{{$price->ending_quantity}}</strong>
                            <br>
                            {{str_plural($productFamily->component->unit_name)}}
                        @else
                            <strong>Unlimited</strong> number of
                            <br>
                            {{str_plural($productFamily->component->unit_name)}}
                        @endif
                    </p>
                @endforeach
            @endif

            @if(!is_null($productFamily->product->trial_interval) && $productFamily->product->trial_interval != 0)
                @if(\Request::route()->getName() == "register.get")
                    <p style="color: #78a300;">
                        {{$productFamily->product->trial_interval}} {{$productFamily->product->trial_interval_unit}}
                        {{$productFamily->product->trial_price_in_cents == 0 ? "free" : ""}}
                        Trial
                    </p>
                @endif
            @endif

            <div class="text-center">
                @if($productFamily->product->initial_charge_in_cents != 0)
                    <div class="text-center">Initial Setup
                        ${{number_format($productFamily->product->initial_charge_in_cents/100, 2)}}</div>
                    <div class="text-center">
                        <i class="fa fa-plus"></i>
                    </div>
                @endif
                <div style="font-weight: bold; color: #78a300;">
                    @if(!is_null($productFamily->preview))
                        ${{number_format($productFamily->preview->next_billing_manifest->total_in_cents/100, 2)}}
                    @else
                        ${{number_format($productFamily->product->price_in_cents/100, 2)}} (GST exc)
                    @endif
                </div>
                <span class="text-sm">
        {{$productFamily->product->trial_interval_unit}}-to-{{$productFamily->product->trial_interval_unit}}
        </span>
            </div>
        </div>
    @endforeach

    <div class="row">
        <div class="col-sm-12 text-center">
            <div class="form-group form-inline">
                <label for="" class="sl-control-label">Have a Coupon Code?</label>
                &nbsp;
                <input type="text" class="form-control sl-form-control" id="visual-coupon-code">
            </div>
        </div>
    </div>
@endif
