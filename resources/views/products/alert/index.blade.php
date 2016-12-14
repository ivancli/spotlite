@extends('layouts.adminlte')
@section('title', 'Alerts')

@section('breadcrumbs')
    {{--    {!! Breadcrumbs::render('alert_index') !!}--}}
@stop

@section('content')
    <style>
        #tbl-report-task .popover {
            font-size: 11px;
        }

        #tbl-report-task .popover .popover-content {
            padding: 5px 7px;
        }
    </style>
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
                    <!-- Morris chart - Sales -->
                    <div class="chart tab-pane active" id="alert-settings">

                        <table class=" table table-striped table-condensed table-bordered" id="tbl-alert">
                            <thead>
                            <tr>
                                <th class="text-muted">Alert source</th>
                                <th class="text-muted">Trigger</th>
                                <th class="text-muted">Trend</th>
                                <th class="text-muted">Price point</th>
                                <th class="text-muted">Last sent</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
        var tblAlert = null;
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
            tblAlert = $("#tbl-alert").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "pageLength": 25,
                "order": [[4, "desc"]],
                "language": {
                    "emptyTable": "No alerts in the list",
                    "zeroRecords": "No alerts in the list"
                },
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'<\"toolbar-bottom-left\">><'col-sm-7'p>>",
                "ajax": {
                    "url": "{{route('alert.index')}}",
                    "data": function (d) {
                        $.each(d.order, function (index, order) {
                            if (typeof d.columns[d.order[index].column] != "undefined") {
                                d.order[index].column = d.columns[d.order[index].column].name;
                            }
                        });
                    }
                },
                "columns": [
                    {
                        "sortable": false,
                        "name": "alert_owner_type",
                        "data": function (data) {
                            var $cellText = $("<div>").append(
                                    data.alert_owner_type == "product" ? "Product - " : "Site - "
                            );

                            switch (data.alert_owner_type) {
                                case "product":
                                    $cellText.append(
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "return false;",
                                                "data-toggle": "popover",
                                                "data-content": $("<div>").append(
                                                        $("<div>").append(
                                                                "Name: ",
                                                                $("<strong>").text(data.alert_owner.product_name)
                                                        ),
                                                        $("<div>").append(
                                                                "Number of sites: ",
                                                                $("<strong>").text(data.alert_owner.siteCount)
                                                        )
                                                ).html(),
                                                "data-html": true,
                                                "data-trigger": "hover"
                                            }).text(data.alert_owner.product_name)
                                    );
                                    break;
                                case "site":
                                    $cellText.append(
                                            $("<a>").attr({
                                                "href": data.alert_owner.site_url,
                                                "target": "_blank",
                                                "data-toggle": "popover",
                                                "data-content": $("<div>").append(
                                                        $("<div>").append(
                                                                "Domain: ",
                                                                $("<strong>").text(data.alert_owner.domain)
                                                        ),
                                                        $("<div>").append(
                                                                "Last fetch: ",
                                                                $("<strong>").text(timestampToDateTimeByFormat(moment(data.alert_owner.last_crawled_at).unix(), datefmt + " " + timefmt))
                                                        ),
                                                        $("<div>").append(
                                                                "Recent price: ",
                                                                $("<strong>").text('$' + parseFloat(data.alert_owner.recent_price).formatMoney(2, '.', ','))
                                                        )
                                                ).html(),
                                                "data-html": true,
                                                "data-trigger": "hover"
                                            }).text(data.alert_owner.domain)
                                    );
                                    break;
                            }
                            return $("<div>").append($cellText).html();
                        }
                    },
                    {
                        "name": "comparison_price_type",
                        "data": function (data) {
                            return capitalise(data.comparison_price_type)
                        }
                    },
                    {
                        "name": "operator",
                        "data": function (data) {
                            var operatorText = "";
                            switch (data.operator) {
                                case "=<":
                                    operatorText = "Equal or below";
                                    break;
                                case "<":
                                    operatorText = "Below";
                                    break;
                                case "=>":
                                    operatorText = "Equal or above";
                                    break;
                                case ">":
                                    operatorText = "Above";
                                    break;
                            }
                            return operatorText;
                        }
                    },
                    {
                        "name": "comparison_price",
                        "data": function (data) {
                            if (data.comparison_price != null) {
                                return '$' + parseFloat(data.comparison_price).formatMoney(2, '.', ',');
                            } else {
                                return null;
                            }
                        }
                    },
                    {
                        "name": "last_active_at",
                        "data": function (data) {
                            if (data.last_active_at != null) {
                                return timestampToDateTimeByFormat(moment(data.last_active_at).unix(), datefmt + " " + timefmt)
                            } else {
                                return null;
                            }
                        }
                    },
                    {
                        "class": "text-center",
                        "sortable": false,
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<div>").append(
                                            $("<a>").addClass("text-muted").attr({
                                                "href": "#",
                                                "data-url": data.urls['edit'],
                                                "onclick": "showAlertForm(this); return false;",
                                                "data-alert-type": data.alert_owner_type
                                            }).append(
                                                    $("<i>").addClass("glyphicon glyphicon-cog")
                                            ),
                                            "&nbsp;",
                                            $("<a>").addClass("text-danger").attr({
                                                "href": "#",
                                                "data-url": data.urls['delete'],
                                                "data-name": data.alert_owner_type == 'site' ? data.alert_owner.domain : data.alert_owner.product_name,
                                                "onclick": "deleteAlert(this); return false",
                                                "data-alert-type": data.alert_owner_type
                                            }).append(
                                                    $("<i>").addClass("glyphicon glyphicon-trash")
                                            )
                                    )
                            ).html();
                        }
                    }
                ],
                "drawCallback": function (settings) {
                    initialisePopover();
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

        function showAlertForm(el) {
            showLoading();
            $.ajax({
                "url": $(el).attr("data-url"),
                "method": "get",
                "success": function (html) {
                    gaEditAlertFromAlertsPage({
                        "Type": $(el).attr("data-alert-type")
                    });

                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "updateCallback": function (response) {
                                    tblAlert.ajax.reload()
                                },
                                "deleteCallback": function (response) {
                                    tblAlert.ajax.reload()
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $("#modal-alert-site").remove();
                        $("#modal-alert-product").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }

        function deleteAlert(el) {
            deletePopup("Delete Alert", "Are you sure you want to delete the " + $(el).attr("data-name") + " Alert?",
                    "By deleting this alert, you will lose the following data:",
                    [
                        "Related scheduled alert"
                    ],
                    {
                        "affirmative": {
                            "text": "DELETE",
                            "class": "btn-danger btn-flat",
                            "dismiss": true,
                            "callback": function () {
                                showLoading();
                                $.ajax({
                                    "url": $(el).attr("data-url"),
                                    "method": "delete",
                                    "dataType": "json",
                                    "success": function (response) {
                                        hideLoading();
                                        if (response.status == true) {
                                            gaDeleteAlertFromAlertsPage({
                                                "Type": $(el).attr("data-alert-type")
                                            });

                                            tblAlert.row($(el).closest("tr")).remove().draw();
                                        } else {
                                            alertP("Oops! Something went wrong.", "Unable to delete alert, please try again later.");
                                        }
                                    },
                                    "error": function (xhr, status, error) {
                                        hideLoading();
                                        describeServerRespondedError(xhr.status);
                                    }
                                })
                            }
                        },
                        "negative": {
                            "text": "CANCEL",
                            "class": "btn-default btn-flat",
                            "dismiss": true
                        }
                    });
        }
    </script>
@stop