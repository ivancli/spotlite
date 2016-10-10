Product Alert



@if($alert->comparison_price_type == "my price")
    <h4>My Site URL</h4>
    <p>
        {{$mySite->site_url}}
    </p>
    <p>
        {{$mySite->recent_price}}
    </p>
@else
    <h4>Comparison Price</h4>
    <p>{{$alert->comparison_price}}</p>
@endif

<h4>Alert Sites URLs</h4>
<ol>
    @foreach($alertingSites as $alertingSite)
        <li>
            <ul>
                <li>
                    <p>{{$alertingSite->site_url}}</p>
                </li>
                <li>
                    <p>{{$alertingSite->recent_price}}</p>
                </li>
            </ul>
        </li>

    @endforeach
</ol>
<h4>Operator</h4>
{{$alert->operator}}