@extends('layouts.adminlte')
@section('title', 'Alerts')
@section('header_title', "Alerts")

@section('breadcrumbs')
    {!! Breadcrumbs::render('alert_index') !!}
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
        <div class="col-md-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Alerts</h3>
                </div>
                <div class="box-body">
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
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Alert History</h3>
                </div>
                <div class="box-body">
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
@stop

@section('scripts')
    <script type="text/javascript">
        var tblAlert = null;
        var tblAlertLog = null;
        $(function () {
            jQuery.fn.dataTable.Api.register('processing()', function (show) {
                return this.iterator('table', function (ctx) {
                    ctx.oApi._fnProcessingDisplay(ctx, show);
                });
            });
            tblAlert = $("#tbl-alert").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "filter": false,
                "pageLength": 25,
                "order": [[4, "asc"]],
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
                                                                $("<strong>").text(data.alert_owner.product_sites.length)
                                                        )
                                                ).html(),
                                                "data-html": true,
                                                "data-trigger": "hover focus"
                                            }).text(data.alert_owner.product_name)
                                    );
                                    break;
                                case "product_site":
                                    $cellText.append(
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "return false;",
                                                "data-toggle": "popover",
                                                "data-content": $("<div>").append(
                                                        $("<div>").append(
                                                                "Domain: ",
                                                                $("<strong>").text(data.alert_owner.site.domain)
                                                        ),
                                                        $("<div>").append(
                                                                "Last crawled: ",
                                                                $("<strong>").text(data.alert_owner.site.last_crawled_at)
                                                        ),
                                                        $("<div>").append(
                                                                "Recent price: ",
                                                                $("<strong>").text('$' + parseFloat(data.alert_owner.site.recent_price).formatMoney(2, '.', ','))
                                                        )
                                                ).html(),
                                                "data-html": true,
                                                "data-trigger": "hover focus"
                                            }).text(data.alert_owner.site.domain)
                                    );
                                    break;
                            }
                            return $("<div>").append($cellText).html();
                        }
                    },
                    {
                        "name": "comparison_price_type",
                        "data": "comparison_price_type"
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
                        "data": "last_active_at"
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
                                                "onclick": "showAlertForm(this)"
                                            }).append(
                                                    $("<i>").addClass("glyphicon glyphicon-cog")
                                            ),
                                            "&nbsp;",
                                            $("<a>").addClass("text-danger").attr({
                                                "href": "#",
                                                "data-url": data.urls['delete'],
                                                "onclick": "deleteReportTask(this)"
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


            tblAlertLog = $("#tbl-alert-log").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "filter": false,
                "pageLength": 10,
                "ordering": false,
                "dom": "<'row'<'col-sm-12'tr>><'row'<'col-sm-12'p>>",
                "ajax": {
                    "url": "{{route('alert_log.index')}}"
                },
                "columns": [
                    {
                        "name": "alert_activity_log_id",
                        "data": function (data) {
                            var content = JSON.parse(data.content);
                            console.info('content', content.email.alert_email_address);
                            console.info('data', data);
                            var popoverContent = "";
                            var alertOwnerType = "";
                            console.info('data.alert.alert_owner_type', data.alert.alert_owner_type);
                            if (data.alert.alert_owner_type == "product") {
                                alertOwnerType = "Product ";
                                popoverContent = $("<div>").append(
                                        $("<div>").append(
                                                "Name: ",
                                                $("<strong>").text(data.alert.alert_owner.product_name)
                                        ),
                                        $("<div>").append(
                                                "Number of sites: ",
                                                $("<strong>").text(data.alert.alert_owner.product_sites.length)
                                        )
                                ).html()

                            } else {
                                alertOwnerType = "Site ";
                                popoverContent = $("<div>").append(
                                        $("<div>").append(
                                                "Domain: ",
                                                $("<strong>").text(data.alert.alert_owner.site.domain)
                                        ),
                                        $("<div>").append(
                                                "Last crawled: ",
                                                $("<strong>").text(data.alert.alert_owner.site.last_crawled_at)
                                        ),
                                        $("<div>").append(
                                                "Recent price: ",
                                                $("<strong>").text('$' + parseFloat(data.alert.alert_owner.site.recent_price).formatMoney(2, '.', ','))
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
                                                "data-trigger": "hover focus"
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
                        "data": "created_at"
                    }
                ],
                "drawCallback": function (settings) {
                    initialisePopover();
                }
            });


        });

        function initialisePopover() {
            $("[data-toggle=popover]").popover();
        }

        function showAlertForm(el) {
            showLoading();
            $.ajax({
                "url": $(el).attr("data-url"),
                "method": "get",
                "success": function (html) {
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
                        $("#modal-alert-product-site").remove();
                        $("#modal-alert-product").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to show edit alert form, please try again later.");
                }
            });
        }
    </script>
@stop