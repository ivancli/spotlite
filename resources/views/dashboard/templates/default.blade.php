@section('header_title')
    <div class="display-inline-block lbl-dashboard-name">
        {{$dashboard->dashboard_name}}
    </div>
    <div class="display-inline-block txt-dashboard-name vertical-align-middle" style="display: none;">
        <form action="{{$dashboard->urls['update']}}" class="frm-rename-dashboard"
              onsubmit="updateDashboardName(this);return false;">
            <div class="input-group sl-input-group">
                <input type="text" name="dashboard_name" placeholder="Dashboard Name"
                       class="form-control sl-form-control input-lg dashboard-name"
                       value="{{$dashboard->dashboard_name}}">
                <span class="input-group-btn">
                <button type="submit" class="btn btn-default btn-flat btn-lg">
                    <i class="fa fa-pencil"></i>
                </button>
            </span>
            </div>
        </form>
    </div>
    &nbsp;&nbsp;
    <div class="display-inline-block text-muted font-size-14 vertical-align-middle">Created on
        the {{date(auth()->user()->preferences['DATE_FORMAT'], strtotime($dashboard->created_at))}}
        <strong><i>by {{$dashboard->user->first_name}} {{$dashboard->user->last_name}}</i></strong>
        &nbsp; &nbsp; &#124; &nbsp;
        <div class="btn-group vertical-align-text-top cursor-pointer">
            <a type="button" class="text-muted dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <strong>
                    Manage Dashboard <i class="fa fa-cog"></i>
                </strong>
            </a>
            <ul class="dropdown-menu pull-right" role="menu">
                <li><a href="#" onclick="addWidget(); return false;">Add Content to Dashboard</a></li>
                <li><a href="#" onclick="editDashboardName(); return false;">Rename Dashboard</a></li>
                <li><a href="#" onclick="deleteDashboard(this); return false;"
                       data-url="{{$dashboard->urls['delete']}}">Delete Dashboard</a></li>
            </ul>
        </div>
    </div>
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
    <div class="col-lg-3 col-md-4 widget-container widget-placeholder-container">
        @include('dashboard.widget.templates.default_placeholder')
    </div>
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

    function editDashboardName() {
        $(".lbl-dashboard-name").hide();
        $(".txt-dashboard-name").show();
    }

    function updateDashboardName(el) {
        showLoading();
        $.ajax({
            "url": $(el).attr("action"),
            "method": "put",
            "data": $(el).serialize(),
            "dataType": "json",
            "success": function (response) {
                hideLoading();
                if (response.status == true) {
                    $(".txt-dashboard-name").hide()
                    $(".lbl-dashboard-name").text(response.dashboard.dashboard_name).show();
                    $(".lnk-dashboard-" + response.dashboard.dashboard_id).text(response.dashboard.dashboard_name);

                } else {
                    var errorMsg = "Unable to update dashboard name. ";
                    if (response.errors != null) {
                        $.each(response.errors, function (index, error) {
                            errorMsg += error + " ";
                        })
                    }
                    alertP("Error", errorMsg);
                }
            },
            "error": function (xhr, status, error) {
                hideLoading();
                describeServerRespondedError(xhr.status);
            }
        })
    }
</script>