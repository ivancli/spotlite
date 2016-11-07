<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style type="text/css">
    .header {
        background-color: #d3d3d3;
    }

    .text-center {
        text-align: center;
    }
</style>
<table>
    <tr class="header">
        <td>
            <b>Category</b>
        </td>
        <td>
            <b>Product</b>
        </td>
        <td>
            <b>Site</b>
        </td>
        <td>
            <b>Current Price</b>
        </td>
        <td>
            <b>Last Updated</b>
        </td>
        <td>
            <b>Previous Price</b>
        </td>
        <td>
            <b>Change Date</b>
        </td>
    </tr>
    @foreach($data->products as $product)
        @foreach($product->sites as $site)
            <tr>
                <td>{{$data->category_name}}</td>
                <td>{{$product->product_name}}</td>
                <td><a href="{{$site->site_url}}">{{$site->site_url}}</a></td>
                @if(!is_null($site->recent_price))
                    <td>
                        {{$site->recent_price}}
                    </td>
                @else
                    <td class="text-center">
                        -
                    </td>
                @endif
                @if(!is_null($site->last_crawled_at))
                    <td>
                        {{$site->last_crawled_at}}
                    </td>
                @else
                    <td class="text-center">
                        -
                    </td>
                @endif
                @if(!is_null($site->historicalPrices()->orderBy('created_at', 'desc')->where('price', '!=', $site->recent_price)->first()))
                    <td>
                        {{$site->historicalPrices()->orderBy('created_at', 'desc')->where('price', '!=', $site->recent_price)->first()->price}}
                    </td>
                @else
                    <td class="text-center">
                        -
                    </td>
                @endif
                @if(!is_null($site->historicalPrices()->orderBy('created_at', 'desc')->where('price', '!=', $site->recent_price)->first()))
                    <td>
                        {{$site->historicalPrices()->orderBy('created_at', 'asc')->where('price', $site->recent_price)->where('price_id', '>', $site->historicalPrices()->orderBy('created_at', 'desc')->where('price', '!=', $site->recent_price)->first()->getKey())->first()->created_at}}
                    </td>
                @else
                    <td class="text-center">
                        -
                    </td>
                @endif
            </tr>
        @endforeach
    @endforeach
</table>