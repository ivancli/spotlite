<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style type="text/css">
    .header {
        background-color: #7ed0c0;
        color: #ffffff;
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
                <td><a href="{{$site->site_url}}">{{is_null($site->userDomainName) ? $site->domain : $site->userDomainName}}</a></td>
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
                @if(!is_null($site->previousPrice))
                    <td>
                        {{$site->previousPrice->price}}
                    </td>
                @else
                    <td class="text-center">
                        -
                    </td>
                @endif
                @if(!is_null($site->priceLastChangedAt))
                    <td>
                        {{$site->priceLastChangedAt}}
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