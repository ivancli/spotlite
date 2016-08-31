@if(!is_null($products))
    @foreach($products as $item)
        <div class="product-container {{isset($chosenAPIProductIDs) && in_array($item->product->id, $chosenAPIProductIDs) ? 'chosen': ''}}"
             data-link="{{array_first($item->product->public_signup_pages)->url}}"
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
    @endforeach
@endif
