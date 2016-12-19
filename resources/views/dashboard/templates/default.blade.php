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
                    <i class="fa fa-check"></i>
                </button>
            </span>
            </div>
        </form>
    </div>
    &nbsp;&nbsp;
    <div class="display-inline-block text-muted font-size-14">Created on
        the {{date(auth()->user()->preferences['DATE_FORMAT'], strtotime($dashboard->created_at))}}
        <strong><i>by {{$dashboard->user->first_name}} {{$dashboard->user->last_name}}</i></strong>
        &nbsp; &nbsp; &#124; &nbsp;
        <div class="btn-group cursor-pointer" style="vertical-align: baseline;">
            <a type="button" class="text-muted dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="btn-dropdown-manage-dashboard">
                <strong>
                    Manage Dashboard <i class="fa fa-cog"></i>
                </strong>
            </a>
            <ul class="dropdown-menu pull-right" role="menu">
                <li><a href="#" onclick="addWidget(); return false;">Add Chart to Dashboard</a></li>
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
            {{--Welcome to your dashboard. Here you will see all the live feedback of the prices you are tracking. Add a--}}
            {{--price you want to track by clicking on the "Add product price to track" link or go to the "Product Prices"--}}
            {{--within the navigation.--}}
            Welcome to your Dashboard. Here you'll be able to see all prices you're tricking through automatically
            updated charts. Add a product price you want to track by clicking on "Add Product to Track" link or go to
            the Products page on the navigation to add your Categories & Products.
        </p>
    </div>
</div>

<hr class="content-divider-white">

<form class="text-muted dashboard-filter-container" id="frm-dashboard-filter-update"
      action="{{$dashboard->urls['filter_update']}}">
    <span style="font-size: 15px;">FILTER BY:</span>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <select id="sel-timespan" name="timespan" class="form-control sl-form-control form-control-inline"
            onchange="submitUpdateFilters(this);">
        <option value="" {{is_null($dashboard->getPreference('timespan')) ? 'selected' : ''}}>Timespan</option>
        <option value="this_week" {{$dashboard->getPreference('timespan') == 'this_week' ? 'selected' : ''}}>
            This week
        </option>
        <option value="last_week" {{$dashboard->getPreference('timespan') == 'last_week' ? 'selected' : ''}}>
            Last week
        </option>
        <option value="last_7_days" {{$dashboard->getPreference('timespan') == 'last_7_days' ? 'selected' : ''}}>
            Last 7 days
        </option>
        <option value="this_month" {{$dashboard->getPreference('timespan') == 'this_month' ? 'selected' : ''}}>
            This month
        </option>
        <option value="last_month" {{$dashboard->getPreference('timespan') == 'last_month' ? 'selected' : ''}}>
            Last month
        </option>
        <option value="last_30_days" {{$dashboard->getPreference('timespan') == 'last_30_days' ? 'selected' : ''}}>
            Last 30 days
        </option>
        <option value="this_quarter" {{$dashboard->getPreference('timespan') == 'this_quarter' ? 'selected' : ''}}>
            This quarter
        </option>
        <option value="last_quarter" {{$dashboard->getPreference('timespan') == 'last_quarter' ? 'selected' : ''}}>
            Last quarter
        </option>
        <option value="last_90_days" {{$dashboard->getPreference('timespan') == 'last_90_days' ? 'selected' : ''}}>
            Last 90 days
        </option>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <select id="sel-period-resolution" name="resolution" class="form-control sl-form-control form-control-inline"
            onchange="submitUpdateFilters(this);">
        <option value="" {{is_null($dashboard->getPreference('resolution')) ? 'selected' : ''}}>Period Resolution
        </option>
        <option value="daily" {{$dashboard->getPreference('resolution') == 'daily' ? 'selected' : ''}}>
            Daily
        </option>
        <option value="weekly" {{$dashboard->getPreference('resolution') == 'weekly' ? 'selected' : ''}}>
            Weekly
        </option>
        <option value="monthly" {{$dashboard->getPreference('resolution') == 'monthly' ? 'selected' : ''}}>
            Monthly
        </option>
    </select>

    &nbsp;&nbsp;&nbsp;&nbsp;
    <button class="btn btn-default btn-flat" id="btn-reset-dashboard-filter"
            @if(is_null($dashboard->getPreference('timespan')) && is_null($dashboard->getPreference('resolution')))
            style="display: none;"
            @endif
            onclick="submitResetFilters();return false;">RESET FILTER
    </button>
</form>
<hr class="content-divider-white">

<div class="row widgets-container">
    @if($dashboard->widgets->count() > 0)
        @foreach($dashboard->widgets()->orderBy('dashboard_widget_order', 'asc')->get() as $widget)
            <div class="col-lg-3 col-md-4 widget-container" data-widget-url="{{$widget->urls['show']}}">
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

    $(function () {
        $("[data-toggle=popover]").popover();
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
                    alertP("Oops! Something went wrong.", "Unable to update widget order, please try again later.");
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
                    $(".txt-dashboard-name").hide();
                    $(".lbl-dashboard-name").text(response.dashboard.dashboard_name).show();
                    $(".lnk-dashboard-" + response.dashboard.dashboard_id).text(response.dashboard.dashboard_name);

                } else {
                    var errorMsg = "";
                    if (response.errors != null) {
                        $.each(response.errors, function (index, error) {
                            errorMsg += error + " ";
                        })
                    }
                    alertP("Oops! Something went wrong.", errorMsg);
                }
            },
            "error": function (xhr, status, error) {
                hideLoading();
                describeServerRespondedError(xhr.status);
            }
        })
    }

    function submitUpdateFilters(el) {
        showLoading();
        $.ajax({
            "url": $("#frm-dashboard-filter-update").attr("action"),
            "method": "put",
            "data": $("#frm-dashboard-filter-update").serialize(),
            "dataType": "json",
            "success": function (response) {
                hideLoading();
                if (response.status == true) {
                    reloadAllWidgets();
                    updateFilterButtonStatus();
                } else {
                    alertP('Error', 'Unable to update filters, please try again later.');
                }
            },
            "error": function (xhr, status, error) {
                hideLoading();
                describeServerRespondedError(xhr.status);
            }
        })
    }

    function submitResetFilters() {
        showLoading();
        $.ajax({
            "url": "{{route('dashboard.filter.destroy', $dashboard->getKey())}}",
            "method": "delete",
            "dataType": "json",
            "success": function (response) {
                hideLoading();
                if (response.status == true) {
                    /*TODO enhance*/
                    $("#sel-timespan").val("");
                    $("#sel-period-resolution").val("");
                    updateFilterButtonStatus();
                    reloadAllWidgets();
                } else {
                    alertP('Error', 'Unable to update filters, please try again later.');
                }
            },
            "error": function (xhr, status, error) {
                hideLoading();
                describeServerRespondedError(xhr.status);
            }
        })
    }

    function reloadAllWidgets() {
        $(".widget-container").each(function () {
            var $this = $(this);
            $this.slideUp();
            if (!$this.hasClass("widget-placeholder-container")) {
                var url = $this.attr("data-widget-url");
                getWidget(url, function (html) {
                    var $newWidget = $(html).replaceAll($this);
                    $newWidget.slideDown();
                    $("[data-toggle=popover]").popover();
                });
            } else {
                $this.slideDown();
            }
        });
    }

    function updateFilterButtonStatus() {
        var period = $("#sel-period-resolution").val();
        var timespan = $("#sel-timespan").val();

        if (timespan == "" && period == "") {
            $("#btn-reset-dashboard-filter").hide();
        } else {
            $("#btn-reset-dashboard-filter").show();
        }
    }
</script>