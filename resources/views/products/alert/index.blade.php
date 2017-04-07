@extends('layouts.adminlte')
@section('title', 'Alerts')
@section('header_title', "Alerts")

@section('breadcrumbs')
    {{--    {!! Breadcrumbs::render('alert_index') !!}--}}
@stop

@section('head_scripts')
    {{--TOUR--}}
    @if(auth()->user()->categories()->count() > 0)
        <script type="text/javascript" src="{{elixir('js/dashboard-tour.js')}}"></script>
    @else
        <script type="text/javascript" src="{{elixir('js/alert-tour.js')}}"></script>
    @endif
@stop

@section('links')
    <link rel="stylesheet" href="{{elixir('css/tour.css')}}">
@stop

@section('content')
    <style>
        #tbl-report-task .popover {
            font-size: 11px;
        }

        #tbl-report-task .popover .popover-content {
            padding: 5px 7px;
        }

        .fa-play {
            transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            -webkit-transform: rotate(0deg);
            transition: transform 550ms ease;
            -moz-transition: -moz-transform 550ms ease;
            -ms-transition: -ms-transform 550ms ease;
            -o-transition: -o-transform 550ms ease;
            -webkit-transition: -webkit-transform 550ms ease;
        }

        .active > .fa-play {
            transform: rotate(90deg);
            -moz-transform: rotate(90deg);
            -ms-transform: rotate(90deg);
            -o-transform: rotate(90deg);
            -webkit-transform: rotate(90deg);
            transition: transform 550ms ease;
            -moz-transition: -moz-transform 550ms ease;
            -ms-transition: -ms-transform 550ms ease;
            -o-transition: -o-transform 550ms ease;
            -webkit-transition: -webkit-transform 550ms ease;
            color: #696969;
        }

        .checkbox {
            margin-top: 0;
            margin-bottom: 0;
        }

        .lst-category, .lst-product {
            list-style: none;
        }

        .lst-category label,
        .lst-product label {
            font-weight: bold;
        }

        .product-checkbox {
            min-height: 30px;
        }

        /*.product-checkbox:last-child {*/
        /*padding-bottom: 15px;*/
        /*}*/

        .lst-category li,
        .lst-product li {
            position: relative;
        }

        .lst-alert-type > li {
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .lst-category, .lst-product {
            margin-top: 15px;
            margin-bottom: 5px;
            padding-left: 45px;
        }

        .lst-category > li,
        .lst-product > li {
            margin-top: 5px;
            padding-bottom: 10px;
        }

        #txt-search {
            color: #fff !important;
            background-color: #d0d0d0 !important;
            border: none;
        }

        #txt-search::-moz-placeholder {
            color: #fff;
            opacity: 1;
        }

        #txt-search:-ms-input-placeholder {
            color: #fff;
        }

        #txt-search::-webkit-input-placeholder {
            color: #fff;
        }

        .lst-alert-type > li.inactive {
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
            filter: alpha(opacity=50);
            -moz-opacity: 0.5;
            -khtml-opacity: 0.5;
            opacity: 0.5;
        }

        .basic-notifications {
            padding-left: 45px;
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <p class="text-muted font-size-17 m-b-20">
                You can set up Basic or Advanced real-time alerts so you can receive email notifications when your competitors or channels change prices, beat your price or match a specific price
                point you nominate.
            </p>
            <p class="text-muted font-size-17 m-b-20">
                <span class="text-danger">Important:</span> some Categories and Products have frequent price changes and you'll be notified of each one of them, which might result in a large quantity
                of emails. If you don't want to receive many emails, you might want to Schedule a Report instead.
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">

            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle">
                    <li class="active">
                        <a href="#alert-settings" data-toggle="tab" aria-expanded="true">Alert Settings</a>
                    </li>
                    <li class="">
                        <a href="#alert-history" data-toggle="tab" aria-expanded="false">Alert History</a>
                    </li>
                </ul>
                <div class="tab-content">

                    <div class="chart tab-pane active" id="alert-settings">
                        <div class="warning-message-container text-danger m-b-10" style="display: none;">
                            <i class="fa fa-info-circle"></i> &nbsp; For this alert to be set up, you need to
                            <a href="https://spotlitehelp.zendesk.com/hc/en-us/articles/235847887-How-do-I-nominate-My-Site-URL-" class="text-danger" style="text-decoration: underline">nominate your
                                site URL</a>
                        </div>

                        <div class="row">
                            <div class="col-md-9">
                                <p style="padding-left: 20px;">To receive price change email alerts, choose from the following options:</p>
                                <ul class="lst-alert-type text-muted" style="padding-left: 20px; list-style: none;">
                                    <li class="
                                    @if(auth()->user()->alerts()->count() > 0)
                                            active
                                            @endif">
                                        <input type="checkbox" id="rd-alert-type-basic" name="alert_type" data-type="basic" onclick="updateAlertTypeStatus(this);"
                                               @if(auth()->user()->alerts()->count() > 0)
                                               checked="checked"
                                                @endif
                                        >
                                        &nbsp;
                                        <i class="fa fa-play"></i>
                                        &nbsp;
                                        <span>
                                            Basic Alerts - a single alert type across all Categories and Products
                                        </span>
                                        <div class="basic-notifications"
                                             @if(auth()->user()->alerts()->count() == 0)
                                             style="display :none;"
                                                @endif
                                        >
                                            <form id="frm-notification-basic" class="nl-form">
                                                Send alert when
                                                &nbsp;
                                                <select name="notification_type" id="basic-notification-type" onchange="checkCompanyURL();">
                                                    <option value=""> -- select alert type --</option>
                                                    <option value="my price" {{!is_null(auth()->user()->alerts()->first()) && auth()->user()->alerts()->first()->comparison_price_type == 'my price' ? "selected" : ""}}>
                                                        my price was beaten
                                                    </option>
                                                    <option value="price changed" {{!is_null(auth()->user()->alerts()->first()) && auth()->user()->alerts()->first()->comparison_price_type == 'price changed' ? "selected" : ""}}>
                                                        price changes
                                                    </option>
                                                </select>
                                                &nbsp;
                                                in all categories.
                                                <div class="nl-overlay"></div>
                                            </form>
                                        </div>
                                    </li>
                                    <li class="
                                    @if(auth()->user()->categoryAlerts()->count() > 0 || auth()->user()->productAlerts()->count() > 0)
                                            active
                                            @endif
                                            ">
                                        <input type="checkbox" id="rd-alert-type-advanced" name="alert_type" data-type="advanced" onclick="updateAlertTypeStatus(this);"
                                               @if(auth()->user()->categoryAlerts()->count() > 0 || auth()->user()->productAlerts()->count() > 0)
                                               checked="checked"
                                                @endif
                                        >
                                        &nbsp;
                                        <i class="fa fa-play"></i>
                                        &nbsp;
                                        <span>
                                            Advanced Alerts -
                                            <span class="appear-text">individual alerts for Categories and Products</span>
                                            <span class="hidden-text" style="display: none;">Choose Category or Product &amp; triggered by: </span>
                                            &nbsp;&nbsp;
                                            <a href="#" class="text-muted" data-toggle="popover" data-trigger="hover" data-container="body"
                                               data-content="Tick the Category to choose the type of alert. Expand Category to see the Products and choose individual alerts.">
                                                <i class="fa fa-info-circle"></i>
                                            </a>
                                        </span>

                                        <div class="advanced-notifications"
                                             @if(auth()->user()->categoryAlerts()->count() == 0 && auth()->user()->productAlerts()->count() == 0)
                                             style="display: none;"
                                                @endif
                                        >
                                            @if(!is_null(auth()->user()->subscriptionCriteria()) && auth()->user()->subscriptionCriteria()->alert_report == "basic")
                                                <p>
                                                    Please <a href="{{route('subscription.edit', auth()->user()->subscription->getKey())}}">upgrade
                                                        your subscription</a> to set up Advanced Alerts
                                                </p>
                                            @else
                                                <ul class="lst-category text-muted">
                                                    @foreach(auth()->user()->categories as $category)
                                                        <li class="category-checkbox" data-category-name="{{$category->category_name}}">
                                                            <div class="checkbox form-control-inline" onclick="toggleProductList(this);"
                                                                 data-category-id="{{$category->getKey()}}">
                                                                <label for="" class="
                                                        @if($category->productAlerts()->count() > 0)
                                                                        active
                                                                @endif">
                                                                    <input type="checkbox" class="chk-category"
                                                                           @if(!is_null($category->alert))
                                                                           checked="checked"
                                                                           @endif
                                                                           onclick="toggleCategoryNotificationForm(this); event.stopPropagation();">&nbsp;<i class="fa fa-play"></i>&nbsp;&nbsp;
                                                                    {{$category->category_name}}
                                                                </label>
                                                            </div>
                                                            &nbsp;&nbsp;&nbsp;
                                                            <form class="form-control-inline frm-category-notification nl-form"
                                                                  style="display: none;">
                                                                <select class="form-control input-sm form-control-inline sel-category-notification-type"
                                                                        onchange="checkCompanyURL();">
                                                                    <option value=""> -- select alert type --</option>
                                                                    <option value="my price" {{!is_null($category->alert) && $category->alert->comparison_price_type == 'my price' ? "selected" : ""}}>
                                                                        beats my price
                                                                    </option>
                                                                    <option value="price changed" {{!is_null($category->alert) && $category->alert->comparison_price_type == 'price changed' ? "selected" : ""}}>
                                                                        price changes
                                                                    </option>
                                                                </select>
                                                                <div class="nl-overlay"></div>
                                                            </form>
                                                            <ul class="lst-product"
                                                                @if($category->productAlerts()->count() == 0)
                                                                style="display: none;"
                                                                @endif
                                                                id="product-list-{{$category->getKey()}}">
                                                                @foreach($category->products as $product)
                                                                    <li class="product-checkbox" data-product-name="{{$product->product_name}}">
                                                                        <div class="checkbox form-control-inline"
                                                                             data-product-id="{{$product->getKey()}}">
                                                                            <label>
                                                                                <input type="checkbox" class="chk-product"
                                                                                       @if(!is_null($product->alert))
                                                                                       checked="checked"
                                                                                       @endif
                                                                                       onclick="toggleProductNotificationForm(this); event.stopPropagation();">
                                                                                {{$product->product_name}}
                                                                            </label>
                                                                        </div>
                                                                        &nbsp;&nbsp;&nbsp;
                                                                        <form class="form-control-inline frm-product-notification frm-notification-type nl-form"
                                                                              style="display: none;">
                                                                            <select class="form-control input-sm form-control-inline sel-notification-type"
                                                                                    onchange="toggleSpecificPriceInput(this);checkCompanyURL();">
                                                                                <option value=""> -- select alert type --</option>
                                                                                <option value="price changed" {{!is_null($product->alert) && $product->alert->comparison_price_type == "price changed" ? "selected" : ""}}>
                                                                                    price changes
                                                                                </option>
                                                                                <option value="my price" {{!is_null($product->alert) && $product->alert->comparison_price_type == "my price" ? "selected" : ""}}>
                                                                                    beats my price
                                                                                </option>
                                                                                <option value="=<" {{!is_null($product->alert) && $product->alert->comparison_price_type == "specific price" ? "selected" : ""}}>
                                                                                    equal or below a specific price
                                                                                </option>
                                                                            </select>
                                                                            <div class="nl-overlay"></div>
                                                                        </form>
                                                                        &nbsp;&nbsp;&nbsp;
                                                                        <form class="form-control-inline frm-product-notification frm-specific-price nl-form"
                                                                              style="display: none;">
                                                                            $<input type="text" placeholder="enter a price"
                                                                                    class="txt-specific-price"
                                                                                    value="{{!is_null($product->alert) && !is_null($product->alert->comparison_price) ? number_format($product->alert->comparison_price, 2, '.', '') : ""}}"
                                                                            >
                                                                            <div class="nl-overlay"></div>
                                                                        </form>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="txt-search" placeholder="SEARCH CATEGORY/PRODUCT NAME" style="
                                @if(auth()->user()->categoryAlerts()->count() == 0 && auth()->user()->productAlerts()->count() == 0)
                                        display: none;
                                @endif
                                        ">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <button class="btn btn-primary btn-flat" onclick="submitUpdateNotifications(); return false;">CONFIRM</button>
                                <button class="btn btn-default btn-flat" onclick="window.location.reload();">CANCEL</button>
                            </div>
                        </div>
                    </div>


                    <div class="chart tab-pane" id="alert-history">
                        <table class="table table-striped table-condensed table-bordered" id="tbl-alert-log">
                            <thead>
                            <tr>
                                <th>Email</th>
                                <th>Sent at</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        var tblAlertLog = null;
        $(function () {
            $("a[data-toggle=tab][href='#alert-history']").on("shown.bs.tab", function (e) {
                if (tblAlertLog == null) {
                    initAlertLog();
                }
            });

            jQuery.fn.dataTable.Api.register('processing()', function (show) {
                return this.iterator('table', function (ctx) {
                    ctx.oApi._fnProcessingDisplay(ctx, show);
                });
            });

            new NLForm($("#frm-notification-basic").get(0))

            $(".frm-category-notification, .frm-product-notification").each(function () {
                new NLForm(this);
            });

            updateNotificationFormVisibility();
            initialisePopover();

            $("#txt-search").on("input", function () {
                var searchText = $(this).val();
                if (searchText) {
                    $("[data-category-name]").each(function () {
                        var thisCategoryName = $(this).attr("data-category-name");
                        if (thisCategoryName.toLowerCase().indexOf(searchText.toLowerCase()) == -1) {
                            var productMatched = false;
                            $(this).find("[data-product-name]").each(function () {
                                var thisProductName = $(this).attr("data-product-name");
                                if (thisProductName.toLowerCase().indexOf(searchText.toLowerCase()) > -1) {
                                    productMatched = true;
                                    $(this).slideDown();
                                } else {
                                    $(this).slideUp();
                                }
                            });
                            if (productMatched == false) {
                                $(this).slideUp();
                            } else {
                                $(this).slideDown();
                            }
                        } else {
                            $(this).slideDown();
                            var productMatched = false;
                            $(this).find("[data-product-name]").slideDown();
                        }
                    });
                } else {
                    $("[data-category-name]").slideDown();
                    $("[data-product-name]").slideDown();
                }
            });
        });

        function initAlertLog() {
            tblAlertLog = $("#tbl-alert-log").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "filter": false,
                "pageLength": 10,
                "ordering": false,
                "language": {
                    "emptyTable": "No alert logs in the list",
                    "zeroRecords": "No alert logs in the list"
                },
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12'p>>",
                "ajax": {
                    "url": "{{route('alert_log.index')}}"
                },
                "columns": [
                    {
                        "name": "alert_activity_log_id",
                        "data": function (data) {
                            var content = JSON.parse(data.content);
                            var popoverContent = "";
                            var alertOwnerType = "";
                            if (data.alert_activity_log_owner_type == "product") {
                                alertOwnerType = "Product ";
                                popoverContent = $("<div>").append(
                                    $("<div>").append(
                                        "Name: ",
                                        $("<strong>").text(data.alert_activity_log_owner.product_name)
                                    ),
                                    $("<div>").append(
                                        "Number of sites: ",
                                        $("<strong>").text(data.alert_activity_log_owner.siteCount)
                                    )
                                ).html()
                            } else if(data.alert_activity_log_owner_type == "category"){
                                alertOwnerType = "Category ";
                                popoverContent = $("<div>").append(
                                    $("<div>").append(
                                        "Name: ",
                                        $("<strong>").text(data.alert_activity_log_owner.category_name)
                                    )
                                ).html()
                            } else {
                                alertOwnerType = "Site ";
                                popoverContent = $("<div>").append(
                                    $("<div>").append(
                                        "Domain: ",
                                        $("<strong>").text(data.alert_activity_log_owner.domain)
                                    ),
                                    $("<div>").append(
                                        "Last crawled: ",
                                        $("<strong>").text(timestampToDateTimeByFormat(moment(data.alert_activity_log_owner.last_crawled_at).unix(), datefmt + " " + timefmt))
                                    ),
                                    $("<div>").append(
                                        "Recent price: ",
                                        $("<strong>").text('$' + parseFloat(data.alert_activity_log_owner.recent_price).formatMoney(2, '.', ','))
                                    )
                                ).html()
                            }

                            return $("<div>").append(
                                $("<div>").append(
                                    $("<a>").attr({
                                        "href": "#",
                                        "onclick": "return false;",
                                        "data-toggle": "popover",
                                        "data-content": popoverContent,
                                        "data-html": true,
                                        "data-trigger": "hover"
                                    }).addClass("text-muted").text(alertOwnerType),
                                    "alert sent to ",
                                    $("<a>").attr({
                                        "href": "mailto:" + content.email.alert_email_address
                                    }).text(content.email.alert_email_address)
                                )
                            ).html();
                        }
                    },
                    {
                        "name": "created_at",
                        "data": function (data) {
                            return timestampToDateTimeByFormat(moment(data.created_at).unix(), datefmt + " " + timefmt);
                        }
                    }
                ],
                "drawCallback": function (settings) {
                    initialisePopover();
                }
            });

        }

        function initialisePopover() {
            $("[data-toggle=popover]").popover();
        }


        function toggleProductList(el) {
            var categoryId = $(el).attr("data-category-id");
            var $productList = $("ul#product-list-" + categoryId);
            if ($productList.is(":visible")) {
                $productList.slideUp();
                $(el).find(">label").removeClass("active");
            } else {
                $productList.slideDown();
                $(el).find(">label").addClass("active");
            }
        }

        function toggleCategoryNotificationForm(el) {
            if ($(el).is(":checked")) {
                $(el).closest("li.category-checkbox").find(".frm-category-notification").fadeIn();
            } else {
                $(el).closest("li.category-checkbox").find(".frm-category-notification").fadeOut();
            }
        }

        function toggleProductNotificationForm(el) {
            if ($(el).is(":checked")) {
                $(el).closest("li.product-checkbox").find(".frm-notification-type").fadeIn();
                if ($(el).closest("li.product-checkbox").find(".frm-notification-type").val() == "=<") {
                    $(el).closest("li.product-checkbox").find(".frm-specific-price").fadeIn();
                }
            } else {
                $(el).closest("li.product-checkbox").find(".frm-product-notification").fadeOut();
            }
        }

        function toggleSpecificPriceInput(el) {
            if ($(el).val() == "=<") {
                $(el).closest(".product-checkbox").find(".frm-specific-price").fadeIn();
            } else {
                $(el).closest(".product-checkbox").find(".frm-specific-price").fadeOut();
            }
        }

        function updateAlertTypeStatus(el) {
            switch ($(el).attr("data-type")) {
                case "basic":
                    /*disable advanced*/
                    $("#rd-alert-type-advanced").prop("checked", false).closest("li").removeClass("active").addClass("inactive");
                    $(".advanced-notifications").slideUp();
                    if ($(el).is(":checked")) {
                        $(".basic-notifications").slideDown();
                        $(el).closest("li").addClass("active").removeClass("inactive");
                        $("#txt-search").fadeOut();
                        $(".hidden-text").hide();
                        $(".appear-text").show();
                    } else {
                        $(".basic-notifications").slideUp();
                        $(el).closest("li").removeClass("active");
                        $("#rd-alert-type-advanced").closest("li").removeClass("inactive");
                        $("#txt-search").fadeOut();
                        $(".hidden-text").hide();
                        $(".appear-text").show();
                    }
                    break;
                case "advanced":
                    $("#rd-alert-type-basic").prop("checked", false).closest("li").removeClass("active").addClass("inactive");
                    $(".basic-notifications").slideUp();
                    if ($(el).is(":checked")) {
                        $(".advanced-notifications").slideDown();
                        $(el).closest("li").addClass("active").removeClass("inactive");
                        $("#txt-search").fadeIn();
                        $(".hidden-text").show();
                        $(".appear-text").hide();
                    } else {
                        $("#txt-search").fadeOut();
                        $(".advanced-notifications").slideUp();
                        $(el).closest("li").removeClass("active");
                        $("#rd-alert-type-basic").closest("li").removeClass("inactive");
                        $(".hidden-text").hide();
                        $(".appear-text").show();
                    }
                    break;
            }
        }

        function checkCompanyURL() {
            $(".warnning-message-container").hide();
            var show = false;
            $(".sel-category-notification-type, .sel-notification-type, #basic-notification-type").each(function () {
                if ($(this).val() == "my price" && (user.company_url == null || user.company_url.trim() == "")) {
                    show = true;
                }
            });
            if (show == true) {
                $(".warning-message-container").show();
            } else {
                $(".warning-message-container").hide();
            }
        }

        function collectFormData() {
            /*TODO category level data*/
            var categoryNotifications = [];
            var productNotifications = [];

            $(".chk-category:checked").each(function () {
                var $categoryList = $(this).closest(".category-checkbox");
                var categoryId = $categoryList.find("[data-category-id]").attr("data-category-id");
                var notificationType = $categoryList.find(".sel-category-notification-type").val();
                categoryNotifications.push({
                    "category_id": categoryId,
                    "type": notificationType
                })
            });

            $(".chk-product:checked").each(function () {
                var $productList = $(this).closest(".product-checkbox");
                var productId = $productList.find("[data-product-id]").attr("data-product-id");
                var notificationType = $productList.find(".sel-notification-type").val();
                var specificPrice = $productList.find(".txt-specific-price").val();
                productNotifications.push({
                    "product_id": productId,
                    "type": notificationType,
                    "specificPrice": specificPrice
                })
            });
            if ($("#rd-alert-type-basic").is(":checked")) {
                return {
                    "notification_type": $("#basic-notification-type").val(),
                    "email[]": [user.email]
                };
            } else if ($("#rd-alert-type-advanced").is(":checked")) {
                return {
                    "categories": categoryNotifications,
                    "products": productNotifications,
                    "email[]": [user.email]
                };
            }
            return {}
        }

        function updateNotificationFormVisibility() {
            $(".chk-category").each(function () {
                var $chk = $(this);
                var $li = $chk.closest(".category-checkbox");
                if ($chk.is(":checked")) {
                    $li.find(".frm-category-notification").fadeIn();
                } else {
                    $li.find(".frm-category-notification").fadeOut();
                }
            });

            $(".chk-product").each(function () {
                var $chk = $(this);
                var $li = $chk.closest(".product-checkbox");
                if ($chk.is(":checked")) {
                    $li.find(".frm-notification-type").fadeIn();
                    if ($li.find(".sel-notification-type").val() == "=<") {
                        $li.find(".frm-specific-price").fadeIn();
                    } else {
                        $li.find(".frm-specific-price").fadeOut();
                    }
                } else {
                    $li.find(".frm-notification-type").fadeOut();
                    $li.find(".frm-specific-price").fadeOut();
                }
            });
        }

        function submitUpdateNotifications(successCallback, failCallback) {
            /*TODO collect data*/
            var data = collectFormData();
            showLoading();
            $.ajax({
                "url": "alert/set_up_notifications",
                "method": "put",
                "data": data,
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if ($.isFunction(successCallback)) {
                        successCallback(response);
                    }

                    alertP("Update Alert Settings", "Alert Settings is updated.");
//                    $("#modal-set-up-notifications").modal("hide");
//                    if (data.notification_type == "" && data.products.length == 0 && data.categories.length == 0) {
//                        $("#btn-set-up-alerts").empty().append(
//                            $("<i>").addClass("fa fa-bell-o"),
//                            " &nbsp; SET UP ALERTS"
//                        );
//                    } else {
//                        $("#btn-set-up-alerts").empty().append(
//                            $("<i>").addClass("fa fa-bell ico-alert-enabled"),
//                            " &nbsp; MANAGE ALERTS"
//                        );
//                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                        var errorMsg = "";
                        $.each(xhr.responseJSON, function (key, error) {
                            $.each(error, function (index, message) {
                                errorMsg += message + " ";
                            })
                        });
                        alertP("Oops! Something went wrong.", errorMsg);
                    } else {
                        describeServerRespondedError(xhr.status);
                    }
                    if ($.isFunction(failCallback)) {
                        failCallback(xhr, status, error);
                    }
                }
            });
        }
    </script>

@stop