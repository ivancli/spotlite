<table>
    <thead>
    <tr>
        <th>Category</th>
        <th>Product</th>
        <th>Reference site price</th>
        <th>Cheapest</th>
        <th>Cheapest $</th>
        <th>Difference $</th>
        <th>Difference %</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr
        @if($product->reference_recent_price== $product->cheapest_recent_price)
            style="color: #43bda5 !important"
                @endif
        >
            <td>{{ $product->category_name }}</td>
            <td>{{ $product->product_name }}</td>
            <td>
                @if(isset($product->reference_recent_price) && !is_null($product->reference_recent_price))
                    ${{ number_format($product->reference_recent_price, 2) }}
                @else
                    n/a
                @endif
            </td>
            <td>
                @if(isset($product->cheapest_site_url))
                    @foreach(explode('$ $', $product->cheapest_site_url) as $cheapestSite)
                        {{--@if(isset(explode('$#$', $cheapestSite)[1]))--}}
                            {{--<a href="{{ explode('$#$', $cheapestSite)[0] }}">{{ explode('$#$', $cheapestSite)[1] }}</a>--}}
                        {{--@else--}}
                            {{--<a href="{{ explode('$#$', $cheapestSite)[0] }}">{{ explode('$#$', $cheapestSite)[0] }}</a>--}}
                        {{--@endif--}}
                        {{ explode('$#$', $cheapestSite)[0] }}
                    @endforeach
                @endif
            </td>
            <td>
                @if(isset($product->cheapest_recent_price) && !is_null($product->cheapest_recent_price))
                    ${{ number_format($product->cheapest_recent_price, 2) }}
                @else
                    n/a
                @endif
            </td>
            <td>
                @if(isset($product->diff_cheapest) && !is_null($product->diff_cheapest))
                    ${{ number_format($product->diff_cheapest, 2) }}
                @else
                    n/a
                @endif
            </td>
            <td>
                @if(isset($product->percent_diff_cheapest) && !is_null($product->percent_diff_cheapest))
                    {{ number_format($product->percent_diff_cheapest*100, 2) }}%
                @else
                    n/a
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>