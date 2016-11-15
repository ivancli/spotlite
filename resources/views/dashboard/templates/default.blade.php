@section('header_title')
    {{$dashboard->dashboard_name}}
    <div class="pull-right">
        <button class="btn {{!is_null($dashboard->getPreference('timespan')) ? 'btn-success' : 'btn-primary'}} btn-sm btn-flat"
                onclick="applyFilters();">
            @if(!is_null($dashboard->getPreference('timespan')))
                Update Filters
            @else
                Apply Filters
            @endif
        </button>
        <button class="btn btn-primary btn-sm btn-flat" onclick="addWidget();">Add Content</button>
    </div>
@stop

<div class="row widgets-container">
    @if($dashboard->widgets->count() > 0)
        @foreach($dashboard->widgets()->orderBy('dashboard_widget_order', 'asc')->get() as $widget)
            <div class="col-lg-3 col-md-4 widget-container">
                @if(!is_null($widget->widgetType) && !is_null($widget->widgetType->template))
                    @include('dashboard.widget.templates.'.$widget->widgetType->template->dashboard_widget_template_name)
                @endif
            </div>
        @endforeach
    @endif
</div>


<script type="text/javascript">

    widgetDrake = dragula([$(".widgets-container").get(0)]).on('drop', function (el, target, source, sibling) {
        updateWidgetOrder();
    });

    function updateWidgetOrder() {
        assignWidgetOrderNumber();
        var orderList = [];
        $(".widget-container").filter(function () {
            return !$(this).hasClass("gu-mirror");
        }).each(function () {
            if ($(this).find("[data-id]").attr("data-id")) {
                var widgetId = $(this).find("[data-id]").attr("data-id");
                var widgetOrder = parseInt($(this).find("[data-order]").attr("data-order"));
                orderList.push({
                    "dashboard_widget_id": widgetId,
                    "dashboard_widget_order": widgetOrder
                });
            }
        });
        $.ajax({
            "url": "{{route('dashboard.widget.order.update')}}",
            "method": "put",
            "data": {
                "widget_order": orderList
            },
            "dataType": 'json',
            "success": function (response) {
                if(response.status != true){
                    alertP("Error", "Unable to update widget order, please try again later.");
                }
            },
            "error": function (xhr, status, error) {
                alertP("Error", "Unable to update widget order, please try again later.");
            }
        })
    }

    function assignWidgetOrderNumber() {
        $(".widget-container").each(function (index) {
            $(this).find("[data-order]").attr("data-order", index + 1);
        });
    }
</script>