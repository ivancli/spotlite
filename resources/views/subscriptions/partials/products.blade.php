@if(!is_null($products))
    @foreach($products as $item)
        <div class="product-container {{isset($chosenAPIProductIDs) && in_array($item->product->id, $chosenAPIProductIDs) ? 'chosen': ''}}"
             data-link="{{array_first($item->product->public_signup_pages)->url}}"
             data-id="{{$item->product->id}}" style="border: 1px solid lightgrey; border-radius: 20px;">
            <h4 style="text-transform: uppercase; color: #78a300;">{{$item->product->name}}</h4>
            {{--product_id: {{$item->product->id}}--}}
            {!! $item->product->description !!}

            @if(!is_null($item->product->trial_interval) && $item->product->trial_interval != 0)
                <p style="color: #78a300;">
                    {{$item->product->trial_interval}} {{$item->product->trial_interval_unit}}
                    {{$item->product->trial_price_in_cents == 0 ? "free" : ""}}
                    Trial
                </p>
            @endif

            <div class="text-center">
                @if($item->product->initial_charge_in_cents != 0)
                    <div class="text-center">Initial Setup
                        ${{number_format($item->product->initial_charge_in_cents/100, 2)}}</div>
                    <div class="text-center">
                        <i class="fa fa-plus"></i>
                    </div>
                @endif
                <div style="font-weight: bold; color: #78a300;">
                    ${{number_format($item->product->price_in_cents/100, 2)}}
                </div>
                <span class="text-sm">month-to-month</span>
            </div>
        </div>
    @endforeach
@endif
