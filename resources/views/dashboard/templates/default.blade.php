@section('header_title')
    {{$dashboard->dashboard_name}}
    <div class="pull-right">
        <button class="btn btn-primary btn-sm">Apply Filters</button>
        <button class="btn btn-primary btn-sm" onclick="addWidget();">Add Content</button>
    </div>
@stop

<div class="row widgets-container">
    @if($dashboard->widgets->count() > 0)
        @foreach($dashboard->widgets as $widget)
            <div class="col-lg-3 col-md-4 widget-container">
                @if(!is_null($widget->widgetType) && !is_null($widget->widgetType->template))
                    @include('dashboard.widget.templates.'.$widget->widgetType->template->dashboard_widget_template_name)
                @endif
            </div>
        @endforeach
    @endif
</div>