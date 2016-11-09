<style>
    #plans, #plans ul, #plans ul li {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    #pricePlans:after {
        content: '';
        display: table;
        clear: both;
    }

    #pricePlans {
        zoom: 1;
    }

    #pricePlans {
        max-width: 69em;
        margin: 0 auto;
    }

    #pricePlans #plans .plan {
        background: #fff;
        float: left;
        width: 100%;
        text-align: center;
        border-radius: 5px;
        margin: 0 0 20px 0;

        -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .planContainer .title h2 {
        font-size: 2.125em;
        font-weight: 300;
        color: #3e4f6a;
        margin: 0;
        padding: .6em 0;
    }

    .planContainer .recommended p {
        background: #3e4f6a;

        background: -webkit-linear-gradient(top, #475975, #364761);
        background: -moz-linear-gradient(top, #475975, #364761);
        background: -o-linear-gradient(top, #475975, #364761);
        background: -ms-linear-gradient(top, #475975, #364761);
        background: linear-gradient(top, #475975, #364761);
        color: #fff;
        font-size: 1.2em;
        font-weight: 700;
        height: 2.6em;
        line-height: 2.6em;
        margin: 0;
        border-radius: 5px 5px 0 0;
    }

    .planContainer .price p {
        background: #3e4f6a;

        background: -webkit-linear-gradient(top, #475975, #364761);
        background: -moz-linear-gradient(top, #475975, #364761);
        background: -o-linear-gradient(top, #475975, #364761);
        background: -ms-linear-gradient(top, #475975, #364761);
        background: linear-gradient(top, #475975, #364761);
        color: #fff;
        font-size: 1.2em;
        font-weight: 700;
        height: 2.6em;
        line-height: 2.6em;
        margin: 0 0 1em;
    }

    .planContainer .price p span {
        color: #8394ae;
    }

    .planContainer .options {
        margin-top: 10em;
    }

    .planContainer .options li {
        font-weight: 700;
        color: #364762;
        line-height: 2.5;
    }

    .planContainer .options li span {
        font-weight: 400;
        color: #979797;
    }

    .planContainer .button a {
        text-transform: uppercase;
        text-decoration: none;
        color: #3e4f6a;
        font-weight: 700;
        letter-spacing: 3px;
        line-height: 2.4em;
        border: 2px solid #3e4f6a;
        display: inline-block;
        width: 80%;
        height: 2.8em;
        border-radius: 4px;
        margin: 1.5em 0 1.8em;
    }

    #credits {
        text-align: center;
        font-size: .8em;
        font-style: italic;
        color: #777;
    }

    #credits a {
        color: #333;
    }

    #credits a:hover {
        text-decoration: none;
    }

    .planContainer.bestPlan .price p {
        background: #f7814d;
    }

    .planContainer.bestPlan .title h2 {
        background: #3e4f6a;

        background: -webkit-linear-gradient(top, #475975, #364761);
        background: -moz-linear-gradient(top, #475975, #364761);
        background: -o-linear-gradient(top, #475975, #364761);
        background: -ms-linear-gradient(top, #475975, #364761);
        background: linear-gradient(top, #475975, #364761);
        color: #fff;
        border-radius: 5px 5px 0 0;
    }

    .planContainer.bestPlan .button a {
        color: #fff;
        background: #f7814d;
        border: 2px solid #f7814d;
    }

    .planContainer.bestPlan .button a:hover {
        background: #ff9c70;
        border: 2px solid #ff9c70;
    }

    @media screen and (min-width: 481px) and (max-width: 768px) {

        #pricePlans #plans .plan {
            width: 49%;
            margin: 0 2% 20px 0;
        }

        #pricePlans #plans > li:nth-child(2n) {
            margin-right: 0;
        }

    }

    @media screen and (min-width: 769px) and (max-width: 1024px) {

        #pricePlans #plans .plan {
            width: 49%;
            margin: 0 2% 20px 0;
        }

        #pricePlans #plans > li:nth-child(2n) {
            margin-right: 0;
        }

    }

    @media screen and (min-width: 1025px) {

        #pricePlans {
            margin: 2em auto;
        }

        #pricePlans #plans .plan {
            width: 24%;
            margin: 0 1.33% 20px 0;

            -webkit-transition: all .25s;
            -moz-transition: all .25s;
            -ms-transition: all .25s;
            -o-transition: all .25s;
            transition: all .25s;
        }

        #pricePlans #plans > li:last-child {
            margin-right: 0;
        }

        #pricePlans #plans .plan:hover {
            -webkit-transform: scale(1.04);
            -moz-transform: scale(1.04);
            -ms-transform: scale(1.04);
            -o-transform: scale(1.04);
            transform: scale(1.04);
        }

        .planContainer .button a {
            -webkit-transition: all .25s;
            -moz-transition: all .25s;
            -ms-transition: all .25s;
            -o-transition: all .25s;
            transition: all .25s;
        }

        .planContainer .button a:hover {
            background: #3e4f6a;
            color: #fff;
        }

    }
</style>

@if(!is_null($productFamilies))
    <section id="pricePlans">
        <ul id="plans">
            @foreach($productFamilies as $productFamily)

                <li class="plan"
                    @if(!isset($productFamily->product->criteria->recommended) || $productFamily->product->criteria->recommended != true)
                    style="margin-top: 44px;"
                        @endif
                >
                    <ul class="planContainer">
                        @if(isset($productFamily->product->criteria->recommended) && $productFamily->product->criteria->recommended == true)
                            <li class="recommended"><p>Recommended</p></li>
                        @endif
                        <li class="title"><h2>{{$productFamily->product->name}}</h2></li>
                        <li class="price">
                            <p>
                                @if(!is_null($productFamily->preview))
                                    ${{number_format($productFamily->preview->next_billing_manifest->total_in_cents/100, 2)}}
                                @else
                                    ${{number_format($productFamily->product->price_in_cents/100, 2)}} (GST exc)
                                @endif
                                /
                                <span>{{$productFamily->product->trial_interval_unit}}</span>
                            </p>
                        </li>
                        <li>
                            <ul class="options">

                                @if(!is_null($productFamily->product->criteria))
                                    @if(isset($productFamily->product->criteria->product))
                                        <li>
                                            @if($productFamily->product->criteria->product != 0)
                                                <span>Up to</span> {{$productFamily->product->criteria->product}} {{str_plural('Product', $productFamily->product->criteria->product)}}
                                            @else
                                                Unlimited Products
                                            @endif
                                        </li>
                                    @endif

                                    @if(isset($productFamily->product->criteria->site))
                                        <li>
                                            @if($productFamily->product->criteria->site != 0)
                                                <span>Up to</span> {{$productFamily->product->criteria->site}} {{str_plural('Competitor', $productFamily->product->criteria->site)}}
                                                <span>per Product</span>
                                            @else
                                                Unlimited Competitor <span>Tracking</span>
                                            @endif
                                        </li>
                                    @endif

                                    @if(isset($productFamily->product->criteria->dashboard) && $productFamily->product->criteria->dashboard == true)
                                        <li>
                                            <span>Customisable Dashboard</span>
                                        </li>
                                    @endif

                                    @if(isset($productFamily->product->criteria->alert_report))
                                        <li>
                                            @if($productFamily->product->criteria->alert_report == "basic")
                                                Basic <span>Alerts and Reports</span>
                                            @else
                                                Unlimited <span>Alerts and Reports</span>
                                            @endif
                                        </li>
                                    @endif

                                    @if(isset($productFamily->product->criteria->frequency))
                                        <li>
                                            @if($productFamily->product->criteria->frequency == 24)
                                                <span>Updates</span> Every Day
                                            @else
                                                <span>Updates</span>
                                                Every {{$productFamily->product->criteria->frequency}} Hours
                                            @endif
                                        </li>
                                    @endif

                                    @if(isset($productFamily->product->criteria->historic_pricing))
                                        <li>
                                            @if($productFamily->product->criteria->historic_pricing == 0)
                                                Lifetime <span>Historic Pricing</span>
                                            @else
                                                {{$productFamily->product->criteria->historic_pricing}} {{str_plural('Month', $productFamily->product->criteria->historic_pricing)}}
                                                <span>Historic Pricing</span>
                                            @endif
                                        </li>
                                    @endif

                                    <li
                                            @if(!isset($productFamily->product->criteria->my_price) || $productFamily->product->criteria->my_price != true)
                                            style="visibility: hidden"
                                            @endif
                                    >
                                        'My Price' Nomination
                                    </li>
                                @endif
                            </ul>
                        </li>
                        <li class="button"><a href="#">Select</a></li>
                    </ul>
                </li>
            @endforeach
        </ul>
    </section>

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
