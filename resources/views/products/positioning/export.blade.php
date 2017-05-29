<table>
    <thead>
    <tr>
        <th>Category</th>
        <th>Product</th>
        <th>Brand</th>
        <th>Supplier</th>
        <th>SKU</th>
        <th>Cost price</th>
        <th>Reference site price</th>
        <th>Cheapest site</th>
        <th>Cheapest site URL</th>
        <th>Cheapest $</th>
        <th>Difference $</th>
        <th>Difference %</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr
                @if(isset($product->reference_recent_price) && $product->reference_recent_price == $product->cheapest_recent_price)
                style="color: #43bda5 !important"
                @endif
        >
            <td>{{ $product->category_name }}</td>
            <td>{{ $product->product_name }}</td>
            <td>{{ $product->brand }}</td>
            <td>{{ $product->supplier }}</td>
            <td>{{ $product->sku }}</td>
            <td>
                @if(!is_null($product->cost_price))
                    ${{ number_format(floatval($product->cost_price), 2) }}
                @endif
            </td>
            <td>
                @if(isset($product->reference_recent_price) && !is_null($product->reference_recent_price))
                    ${{ number_format($product->reference_recent_price, 2) }}
                @else
                    n/a
                @endif
            </td>
            <th>
                @if(isset($product->cheapest_site_url))
                    @foreach(explode('$ $', $product->cheapest_site_url) as $index=>$cheapestSite)
                        @if(isset(explode('$#$', $cheapestSite)[1]))
                            {{ explode('$#$', $cheapestSite)[1] }}@if(count(explode('$ $', $product->cheapest_site_url)) > 1)<br>@endif
                        @endif
                    @endforeach
                @endif
            </th>
            <td>
                @if(isset($product->cheapest_site_url))
                    @foreach(explode('$ $', $product->cheapest_site_url) as $cheapestSite)
                        {{ explode('$#$', $cheapestSite)[0] }}@if(count(explode('$ $', $product->cheapest_site_url)) > 1)<br>@endif
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
                @if(isset($product->reference_recent_price) && floatval($product->cheapest_recent_price) - floatval($product->reference_recent_price) == 0)
                    @if(count(explode('$ $', $product->cheapest_site_url)) > 1)
                        0
                    @else
                        @if(isset($product->diff_second_cheapest) && !is_null($product->diff_second_cheapest))
                            +${{ number_format($product->diff_second_cheapest, 2) }}
                        @else
                            n/a
                        @endif
                    @endif
                @else
                    @if(isset($product->diff_cheapest) && !is_null($product->diff_cheapest))
                        -${{ number_format($product->diff_cheapest, 2) }}
                    @else
                        n/a
                    @endif
                @endif
            </td>
            <td>
                @if(isset($product->reference_recent_price) && floatval($product->cheapest_recent_price) - floatval($product->reference_recent_price) == 0)
                    @if(count(explode('$ $', $product->cheapest_site_url)) > 1)
                        0
                    @else
                        @if(isset($product->percent_diff_second_cheapest) && !is_null($product->percent_diff_second_cheapest))
                            +{{ number_format($product->percent_diff_second_cheapest*100, 2) }}%
                        @else
                            n/a
                        @endif
                    @endif
                @else
                    @if(isset($product->percent_diff_cheapest) && !is_null($product->percent_diff_cheapest))
                        -{{ number_format($product->percent_diff_cheapest*100, 2) }}%
                    @else
                        n/a
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>