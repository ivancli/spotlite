<p>
    We found that at <a href="{{$alert->alertable->site_url}}">
        @if(!is_null($alert->alertable->ebayItem))
            {{ $alert->alertable->ebayItem->seller_username }}
        @elseif
            {{ $alert->alertable->userDomainName }}
        @else
            {{ $alert->alertable->domain }}
        @endif
    </a>
    the price for {{$alert->alertable->product->product_name}} is
    @if($alert->comparison_price_type == "my price")
        @if($alert->alertable->recent_price > $mySite->recent_price)
            above
        @elseif($alert->alertable->recent_price == $mySite->recent_price)
            equal to
        @elseif($alert->alertable->recent_price < $mySite->recent_price)
            below
        @endif
        ${{number_format($mySite->recent_price, 2, '.', ',')}}.
    @else
        @if($alert->alertable->recent_price > $alert->comparison_price)
            above
        @elseif($alert->alertable->recent_price == $alert->comparison_price)
            equal to
        @elseif($alert->alertable->recent_price < $alert->comparison_price)
            below
        @endif
        ${{number_format($alert->comparison_price, 2, '.', ',')}}.
    @endif
</p>

<p>You can also view this information through your <a href="{{route('dashboard.index')}}">SpotLite Dashboard</a>.</p>

<p>Want to change your alert preference? <a href="{{route('alert.index')}}">Click here</a></p>

<p>Best regards,</p>
<p>SpotLite Team</p>

