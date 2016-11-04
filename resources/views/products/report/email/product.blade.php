<p>
    Dear
    @if(!is_null($reportTask->reportable->user) && trim($reportTask->reportable->user->first_name) != "")
        {{$reportTask->reportable->user->first_name}},
    @else
        SpotLite user,
    @endif
</p>

<p>
    Please find your SpotLite product report for {{$reportTask->reportable->product_name}} attached.
</p>

<p>If you have any questions or queries, please email <a href="mailto:support@spotlite.com.au">support@spotlite.com.au</a>.</p>


<p>Regards,</p>
<p>The SpotLite Team</p>