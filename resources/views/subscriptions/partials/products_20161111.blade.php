@if(!is_null($productFamilies))
    @foreach($productFamilies as $productFamily)
        <div class="product-container m-b-10
        {{isset($chosenAPIProductID) && $productFamily->product->id == $chosenAPIProductID ? 'chosen': ''}}
        {{(is_null(old("api_product_id")) && \Request::route()->getName() == "register.get"  && \Request::has("pid") && \Request::get("pid") == $productFamily->product->id) ? 'selected' : ''}}
        {{old("api_product_id") == $productFamily->product->id ? "selected" : ""}}
                "
             data-link="{{array_first($productFamily->product->public_signup_pages)->url}}"
             data-id="{{$productFamily->product->id}}"
             data-price="{{$productFamily->product->price_in_cents}}">
            <h4 style="text-transform: uppercase; color: #78a300;">{{$productFamily->product->name}}</h4>
            {{--criteria--}}
            @if(!is_null($productFamily->product->criteria))
                @if(isset($productFamily->product->criteria->product))
                    <p>
                        @if($productFamily->product->criteria->product != 0)
                            Up
                            to {{$productFamily->product->criteria->product}} {{str_plural('Product', $productFamily->product->criteria->product)}}
                        @else
                            Unlimited Products
                        @endif
                    </p>
                @endif

                @if(isset($productFamily->product->criteria->site))
                    <p>
                        @if($productFamily->product->criteria->site != 0)
                            Up
                            to {{$productFamily->product->criteria->site}} {{str_plural('Competitor', $productFamily->product->criteria->site)}}
                            per Product
                        @else
                            Unlimited Competitor Tracking
                        @endif
                    </p>
                @endif

                @if(isset($productFamily->product->criteria->dashboard) && $productFamily->product->criteria->dashboard == true)
                    <p>
                        Customisable Dashboard
                    </p>
                @endif

                @if(isset($productFamily->product->criteria->alert_report))
                    <p>
                        @if($productFamily->product->criteria->alert_report == "basic")
                            Basic Alerts and Reports
                        @else
                            Unlimited Alerts and Reports
                        @endif
                    </p>
                @endif

                @if(isset($productFamily->product->criteria->frequency))
                    <p>
                        @if($productFamily->product->criteria->frequency == 24)
                            Updates Every Day
                        @else
                            Updates Every {{$productFamily->product->criteria->frequency}} Hours
                        @endif
                    </p>
                @endif

                @if(isset($productFamily->product->criteria->historic_pricing))
                    <p>
                        @if($productFamily->product->criteria->historic_pricing == 0)
                            Lifetime Historic Pricing
                        @else
                            {{$productFamily->product->criteria->historic_pricing}} {{str_plural('Month', $productFamily->product->criteria->historic_pricing)}}
                            Historic Pricing
                        @endif
                    </p>
                @endif

                @if(isset($productFamily->product->criteria->my_price) && $productFamily->product->criteria->my_price == true)
                    <p>
                        'My Price' Nomination
                    </p>
                @endif
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
