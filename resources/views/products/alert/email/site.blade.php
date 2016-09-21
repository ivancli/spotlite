Site Alert



@if($alert->comparison_price_type == "my price")
    <h4>My Site URL</h4>
    <p>
        {{$myProductSite->site->site_url}}
    </p>
    <p>
        {{$myProductSite->site->recent_price}}
    </p>
@else
    <h4>Comparison Price</h4>
    <p>{{$alert->comparison_price}}</p>
@endif

<h4>Alert Site URL</h4>
<p>
    {{$alert->alertable->site->site_url}}
</p>
<p>
    {{$alert->alertable->site->recent_price}}
</p>

<h4>Operator</h4>
{{$alert->operator}}