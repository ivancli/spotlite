<style type="text/css">

    /* -----------------------------------------
    -  $Pricing section
    ----------------------------------------- */
    .section-pricing {
        padding: 115px 0;
    }

    .section-pricing h2 {
        margin-bottom: 15px;
    }

    .section-pricing .sub-text {
        font-family: 'Flama Book';
        font-size: 20px;
        text-align: center;
    }

    .pricing-level {
        box-shadow: 0px 1px 5px 0px rgba(0, 0, 0, 0.2);
        margin-bottom: 30px;
    }

    .pricing-level .lead-text {
        font-weight: 500;
        font-size: 27px;
        color: #494949;
    }

    .pricing-level .price-month {
        font-weight: 300;
        font-size: 33px;
        color: #494949;
    }

    .pricing-level .price-month span {
        font-size: 15px;
        text-transform: uppercase;
    }

    .pricing-level header {
        background-color: #f7f8fa;
        padding: 20px;
    }

    .pricing-level .pricing-body {
        padding: 25px 10px 10px 10px;
        background-color: #fff;
    }

    .pricing-level .pricing-body ul {
        padding-bottom: 20px;
        border-bottom: 1px solid #d8d8d8;
        list-style-image: url("/build/images/green-tick.png");
    }

    .pricing-level .pricing-body ul li {
        margin-bottom: 10px;
    }

    .pricing-level footer {
        padding: 0 20px 20px 20px;
        background-color: #fff;
    }

    .pricing-level footer .button {
        padding: 10px 7px;
        width: 100%;
    }

    .pricing-level footer span {
        display: inline-block;
        text-align: center;
        width: 100%;
        font-size: 13px;
    }

    .pricing-level.recommended header {
        background-color: #e4fbf6;
        padding: 40px 20px 20px 20px;
    }

    .pricing-level.recommended .price-month,
    .pricing-level.recommended .lead-text {
        font-weight: 700;
        font-size: 33px;
    }

    .pricing-level.recommended .price-month span {
        font-weight: 500;
    }

    .trapezoid {
        border-bottom: 40px solid #78d0be;
        border-left: 30px solid transparent;
        border-right: 30px solid transparent;
        height: 0;
        width: 100%;
        position: relative;
    }

    .trapezoid span {
        color: #fff;
        text-transform: uppercase;
        font-size: 16px;
        font-weight: 500;
        position: absolute;
        left: 50%;
        bottom: -32px;
        transform: translateX(-50%);
    }

    @media (min-width: 992px) {
        .recommend-outer {
            margin-top: -41px;
        }
    }

    .underline {
        text-decoration: underline;
    }

    /* -----------------------------------------
    -  $Buttons
    ----------------------------------------- */
    a.button {
        display: inline-block;
        font-family: "Flama" !important;
    }

    .button {
        font-family: "Flama" !important;
        text-transform: uppercase;
        text-align: center;
        color: #fff;
        padding: 15px 25px;
        transition: all 0.5s ease;
        font-size: 15px;
        font-weight: 500;
        letter-spacing: 1px;
    }

    .button:focus, .button:active {
        color: #fff;
    }

    .button-orange {
        background-color: #F8A66F;
        border-bottom: 4px solid #f2710d;
    }

    .button-orange:hover {
        background-color: #78d0be;
        border-bottom: 4px solid #599488;
        color: #fff;
    }

    .button-orange:disabled {
        background-color: #d3d3d3;
        border-bottom-color: #808080;
    }

    .button-orange:disabled:hover {
        background-color: #d3d3d3;
        border-bottom-color: #808080;
    }

    .button-green {
        background-color: #78d0be;
        border-bottom: 4px solid #599488;
    }

    .button-green:hover {
        background-color: #F8A66F;
        border-bottom: 4px solid #f2710d;
        color: #fff;
    }

    .button-blue {
        background-color: #72bbdb;
        border-bottom: 4px solid #0098c7;
    }

    .button-blue:hover {
        background-color: #78d0be;
        border-bottom: 4px solid #599488;
        color: #fff;
    }

    a.button.disabled, a.button.disabled:hover, a.button.disabled:focus, a.button.disabled:active {
        background-color: #F8A66F !important;
        border-bottom: 4px solid #f2710d !important;
        cursor: default;
    }
    .plan .selected-header header{
        background-color: #fdeed5;
    }
</style>


@if(!is_null($productFamilies))
    <section id="pricePlans" style="margin-top: 50px;">
        <div class="row">
            @foreach($productFamilies as $productFamily)
                <div class="col-lg-3 col-md-6 plan"
                     data-link="{{array_first($productFamily->product->public_signup_pages)->url}}"
                     data-id="{{$productFamily->product->id}}"
                     data-price="{{$productFamily->product->price_in_cents}}">
                    <div>
                        @if(\Request::route()->getName() == "subscription.back" && isset($productFamily->product->criteria->recommended) && $productFamily->product->criteria->recommended == true)
                            <div class="recommend-outer">
                                <div class="trapezoid">
                                    <span>Recommended</span>
                                </div>
                                @endif
                                <div class="pricing-level
                                 @if(\Request::route()->getName() == "subscription.back" && isset($productFamily->product->criteria->recommended) && $productFamily->product->criteria->recommended == true)
                                        recommended
                                        @endif
                                @if(isset($chosenAPIProductID) && $chosenAPIProductID == $productFamily->product->id)
                                        selected-header
                                @endif
                                        ">
                                    <header><p class="lead-text">{{$productFamily->product->name}}</p>
                                        <p class="price-month">
                                            @if(!is_null($productFamily->preview))
                                                ${{number_format($productFamily->preview->next_billing_manifest->total_in_cents/100, 2)}}@else
                                                ${{number_format($productFamily->product->price_in_cents/100, 2)}} (GST
                                                exc)@endif<span>/{{$productFamily->product->trial_interval_unit}}</span>
                                        </p>
                                    </header>
                                    @if(!is_null($productFamily->product->criteria))
                                        <div class="pricing-body">
                                            <ul>
                                                @if(isset($productFamily->product->criteria->product))
                                                    <li>
                                                        @if($productFamily->product->criteria->product != 0)
                                                            Up to
                                                            <strong>{{$productFamily->product->criteria->product}} {{str_plural('Product', $productFamily->product->criteria->product)}}</strong>
                                                        @else
                                                            <strong>Unlimited Products</strong>
                                                        @endif
                                                    </li>
                                                @endif

                                                @if(isset($productFamily->product->criteria->site))
                                                    <li>
                                                        @if($productFamily->product->criteria->site != 0)
                                                            Up to
                                                            <strong>{{$productFamily->product->criteria->site}} {{str_plural('Competitor', $productFamily->product->criteria->site)}}</strong>
                                                            per product
                                                            <span>per Product</span>
                                                        @else
                                                            <strong>Unlimited Competitor</strong> Tracking
                                                        @endif
                                                    </li>
                                                @endif

                                                @if(isset($productFamily->product->criteria->dashboard) && $productFamily->product->criteria->dashboard == true)
                                                    <li>
                                                        Customisable Dashboard
                                                    </li>
                                                @endif

                                                @if(isset($productFamily->product->criteria->alert_report))
                                                    <li>
                                                        @if($productFamily->product->criteria->alert_report == "basic")
                                                            <strong>Basic</strong> Alerts and Reports
                                                        @else
                                                            <strong>Advanced</strong> Alerts and Reports
                                                        @endif
                                                    </li>
                                                @endif
                                                @if(isset($productFamily->product->criteria->frequency))
                                                    <li>
                                                        @if($productFamily->product->criteria->frequency == 24)
                                                            Updates <strong>Every Day</strong>
                                                        @else
                                                            Updates
                                                            <strong>Every {{$productFamily->product->criteria->frequency}} {{str_plural('Hour', $productFamily->product->criteria->frequency)}}</strong>
                                                        @endif
                                                    </li>
                                                @endif
                                                @if(isset($productFamily->product->criteria->historic_pricing))
                                                    <li>
                                                        @if($productFamily->product->criteria->historic_pricing == 0)
                                                            <strong>Lifetime</strong> Historic Pricing
                                                        @else
                                                            <strong>{{$productFamily->product->criteria->historic_pricing}} {{str_plural('Month', $productFamily->product->criteria->historic_pricing)}}</strong>
                                                            Historic Pricing
                                                        @endif
                                                    </li>
                                                @endif
                                                    <li
                                                    @if(!isset($productFamily->product->criteria->my_price) || $productFamily->product->criteria->my_price != true)
                                                        style="visibility: hidden"
                                                            @endif
                                                    >
                                                        <strong>"My Price" Nomination</strong>
                                                    </li>
                                            </ul>
                                        </div>
                                    @endif
                                    <footer><p class="text-center">
                                            <a href="#"
                                               class="button button-blue {{(!is_null(old('api_product_id')) && old('api_product_id') == $productFamily->product->id) || (isset($chosenAPIProductID) &&$chosenAPIProductID == $productFamily->product->id)? 'disabled': '' }}"
                                               @if(\Request::route()->getName() == "subscription.back"))
                                               onclick="subscribeNowOnClick(this);return false;"
                                               @elseif((!is_null(old('api_product_id')) && old('api_product_id') == $productFamily->product->id) || (isset($chosenAPIProductID) && $chosenAPIProductID == $productFamily->product->id))
                                               onclick="return false;"
                                               @else
                                               onclick="submitSubscriptionUpdateOnclick(this);return false;"
                                                    @endif>
                                                @if(isset($chosenAPIProduct))
                                                    @if($chosenAPIProduct->price_in_cents > $productFamily->product->price_in_cents)
                                                        DOWNGRADE
                                                    @elseif($chosenAPIProductID == $productFamily->product->id)
                                                        MY PLAN
                                                    @else
                                                        UPGRADE
                                                    @endif
                                                @else
                                                    Get {{$productFamily->product->name}} pack
                                                @endif
                                            </a>
                                        </p>
                                        {{--@if(!isset($chosenAPIProduct) &&)--}}
                                        {{--<span>1 Month Free for the {{$productFamily->product->name}} Pack</span>--}}
                                        {{--@endif--}}
                                    </footer>
                                </div>
                                @if(\Request::route()->getName() == "subscription.back" && isset($productFamily->product->criteria->recommended) && $productFamily->product->criteria->recommended == true)
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif


