<p>
    Our
    {{ucfirst($reportTask->reportable->category_name)}}
    {{ucfirst($reportTask->frequency)}}
    Report is attached.
</p>

<p>You can also view this report through your <a href="{{route('dashboard.index')}}">SpotLite Dashboard</a>.</p>

<p>Want to change your alert preference? <a href="{{route('report.index')}}">Click here</a></p>

<p>Best regards,</p>
<p>SpotLite Team</p>