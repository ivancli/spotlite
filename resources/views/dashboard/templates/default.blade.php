@section('header_title')
    {{$dashboard->dashboard_name}}
    <div class="pull-right">
        <button class="btn btn-primary btn-sm">Apply Filters</button>
        <button class="btn btn-primary btn-sm">Add Content</button>
    </div>
@stop

<div class="row">
    @if(isset($widgets))
        @foreach($widgets as $widget)
            <div class="col-md-3">

            </div>
        @endforeach
    @endif
</div>