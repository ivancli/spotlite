@section('header_title')
    {{$dashboard->dashboard_name}}
    &nbsp;&nbsp;
    <span style="font-size: 14px;"
          class="text-muted">Created on the {{date(auth()->user()->preferences['DATE_FORMAT'], strtotime($dashboard->created_at))}}
        <strong><i>by {{$dashboard->user->first_name}} {{$dashboard->user->last_name}}</i></strong>
    &nbsp; &nbsp; &#124; &nbsp;
    <a href="#" class="text-muted"><strong>Manage Dashboard <i class="fa fa-cog"></i></strong></a>
    </span>

@stop

@section('breadcrumbs')
    {{--<div class="text-right">--}}
    {{--<button class="btn {{!is_null($dashboard->getPreference('timespan')) ? 'btn-success' : 'btn-primary'}} btn-sm btn-flat"--}}
    {{--onclick="applyFilters();">--}}
    {{--@if(!is_null($dashboard->getPreference('timespan')))--}}
    {{--Update Filters--}}
    {{--@else--}}
    {{--Apply Filters--}}
    {{--@endif--}}
    {{--</button>--}}
    {{--<button class="btn btn-primary btn-sm btn-flat" onclick="addWidget();">Add Content</button>--}}
    {{--</div>--}}
@stop

<div class="row">
    <div class="col-lg-8 col-md-10 col-sm-12">
        <p class="text-muted font-size-17">
            Welcome to your dashboard. Here you will see all the live feedback of the prices you are tracking. Add a
            price you want to track by clicking on the "Add product price to track" link or go to the "Product Prices"
            within the navigation.
        </p>
    </div>
</div>

<hr class="content-divider-white">

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
                if (response.status != true) {
                    alertP("Error", "Unable to update widget order, please try again later.");
                }
            },
            "error": function (xhr, status, error) {
                describeServerRespondedError(xhr.status);
            }
        })
    }

    function assignWidgetOrderNumber() {
        $(".widget-container").each(function (index) {
            $(this).find("[data-order]").attr("data-order", index + 1);
        });
    }
</script>