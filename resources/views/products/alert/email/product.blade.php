<p>
    The price for {{$alert->alertable->product_name}} is found to be
    @if($alert->operator == "=<")
        equal or below
    @elseif($alert->operator == "<")
        below
    @elseif($alert->operator == "=>")
        equal or above
    @elseif($alert->operator == ">")
        above
    @endif
    @if($alert->comparison_price_type == "my price")
        ${{number_format($mySite->recent_price, 2, '.', ',')}}.
    @else
        ${{number_format($alert->comparison_price, 2, '.', ',')}}.
    @endif
</p>

<p>You can also view this information through your <a href="{{route('dashboard.index')}}">SpotLite Dashboard</a>.</p>

<p>Want to change your alert preference? <a href="{{route('alert.index')}}">Click here</a></p>

<p>Best regards,</p>
<p>SpotLite Team</p>