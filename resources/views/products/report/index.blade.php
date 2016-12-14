@extends('layouts.adminlte')
@section('title', 'Reports')

@section('header_title', "Reports")

@section('breadcrumbs')
    {{--    {!! Breadcrumbs::render('report_index') !!}--}}
@stop

@section('content')

    <div class="row">
        <div class="col-sm-12">

            <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs ui-sortable-handle">
                    <li class="active">
                        <a href="#report-schedule" data-toggle="tab" aria-expanded="true">Report Schedule</a>
                    </li>
                    <li class="">
                        <a href="#report-history" data-toggle="tab" aria-expanded="false">Report History</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="chart tab-pane active" id="report-schedule">
                        <table class=" table table-striped table-condensed table-bordered" id="tbl-report-task">
                            <thead>
                            <tr>
                                <th class="text-muted">Report source</th>
                                <th class="text-muted">Schedule</th>
                                <th class="text-muted">File type</th>
                                <th class="text-muted">Last report</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="chart tab-pane" id="report-history">
                        <table class=" table table-striped table-condensed table-bordered" id="tbl-report">
                            <thead>
                            <tr>
                                <th class="text-muted">Name</th>
                                <th class="text-muted">Type</th>
                                <th class="text-muted">Created at</th>
                                <th></th>
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
        var tblReportTask = null;
        var tblReport = null;
        $(function () {
//            $.contextMenu({
//                "selector": '.report-list-container .file-anchor',
//                "items": {
//                    "download": {
//                        "name": "Download",
//                        "callback": function (key, opt) {
//                            var el = opt.$trigger.context;
//                            el.click();
//                        }
//                    },
//                    "delete": {
//                        "name": "Delete",
//                        "callback": function (key, opt) {
//                            var el = opt.$trigger.context;
//                            deleteReport(el, function (response) {
//                                $(el).closest("li").remove();
//                            });
//
//                        }
//                    }
//                }
//            });


            jQuery.fn.dataTable.Api.register('processing()', function (show) {
                return this.iterator('table', function (ctx) {
                    ctx.oApi._fnProcessingDisplay(ctx, show);
                });
            });
            tblReportTask = $("#tbl-report-task").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "pageLength": 25,
                "order": [[3, "desc"]],
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'<\"toolbar-bottom-left\">><'col-sm-7'p>>",
                "language": {
                    "emptyTable": "No report schedules in the list",
                    "zeroRecords": "No report schedules in the list"
                },
                "ajax": {
                    "url": "{{route('report_task.index')}}",
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
                        "name": "report_task_owner_type",
                        "data": function (data) {
                            var $cellText = $("<div>").append(
                                    capitalise(data.report_task_owner_type) + " - "
                            );
                            switch (data.report_task_owner_type) {
                                case "category":
                                    $cellText.append(
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "return false;",
                                                "data-toggle": "popover",
                                                "data-content": $("<div>").append(
                                                        $("<div>").append(
                                                                "Name: ",
                                                                $("<strong>").text(data.report_task_owner.category_name)
                                                        ),
                                                        $("<div>").append(
                                                                "Number of products: ",
                                                                $("<strong>").text(data.report_task_owner.productCount)
                                                        ),
                                                        $("<div>").append(
                                                                "Number of sites: ",
                                                                $("<strong>").text(data.report_task_owner.siteCount)
                                                        )
                                                ).html(),
                                                "data-html": true,
                                                "data-trigger": "hover"
                                            }).text(data.report_task_owner.category_name)
                                    );
                                    break;
                                case "product":
                                    $cellText.append(
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "return false;",
                                                "data-toggle": "popover",
                                                "data-content": $("<div>").append(
                                                        $("<div>").append(
                                                                "Name: ",
                                                                $("<strong>").text(data.report_task_owner.product_name)
                                                        ),
                                                        $("<div>").append(
                                                                "Number of sites: ",
                                                                $("<strong>").text(data.report_task_owner.siteCount)
                                                        )
                                                ).html(),
                                                "data-html": true,
                                                "data-trigger": "hover"
                                            }).text(data.report_task_owner.product_name)
                                    );
                                    break;
                                default:
                            }
                            return $("<div>").append($cellText).html();
                        }
                    },
                    {
                        "sortable": false,
                        "data": function (data) {
                            var summary = "";
                            switch (data.frequency) {
                                case "monthly":
                                    summary += "Monthly on " + data.date + (data.date > 28 ? " or last date of a month" : "");
                                    break;
                                case "weekly":
                                    var dayOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                                    summary += "Weekly on " + dayOfWeek[data.day - 1];
                                    break;
                                case "daily":
                                default:
                                    var time = moment("1970-1-1 " + data.time).format("ha");
                                    summary += "Daily at " + time + (data.weekday_only == "y" ? " weekdays only" : "")
                            }
                            return summary;
                        }
                    },
                    {
                        "sortable": false,
                        "name": "file_type",
                        "data": function (data) {
                            switch (data.file_type) {
                                case "xlsx":
                                    return "Excel 2007-2013"
                            }
                            return null;
                        }
                    },
                    {
                        "name": "last_sent_at",
                        "data": function (data) {
                            if (data.last_sent_at != null) {
                                return timestampToDateTimeByFormat(moment(data.last_sent_at).unix(), datefmt + " " + timefmt);
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
                                                "onclick": "showReportTaskForm(this)",
                                                "data-report-type": data.report_task_owner_type
                                            }).append(
                                                    $("<i>").addClass("glyphicon glyphicon-cog")
                                            ),
                                            "&nbsp;",
                                            $("<a>").addClass("text-danger").attr({
                                                "href": "#",
                                                "data-url": data.urls['delete'],
                                                "data-name": data.report_task_owner_type == "product" ? data.report_task_owner.product_name : data.report_task_owner.category_name,
                                                "onclick": "deleteReportTask(this)",
                                                "data-report-type": data.report_task_owner_type
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


//            loadCategoriesAndProductsWithReports(function (response) {
//                console.info(response);
//                if (typeof response.categories != "undefined") {
//                    $.each(response.categories, function (index, category) {
//                        $(".report-list-container .file-tree").append(
//                                $("<li>").addClass("directory collapsed").append(
//                                        $("<a>").attr({
//                                            "data-category-id": category.category_id,
//                                            "href": "#",
//                                            "onclick": "toggleCategoryFolder(this); return false;"
//                                        }).text("Category reports: " + category.category_name)
//                                )
//                        )
//                    });
//                }
//                if (typeof response.products != "undefined") {
//                    $.each(response.products, function (index, product) {
//                        $(".report-list-container .file-tree").append(
//                                $("<li>").addClass("directory collapsed").append(
//                                        $("<a>").attr({
//                                            "data-product-id": product.product_id,
//                                            "href": "#",
//                                            "onclick": "toggleProductFolder(this); return false;"
//                                        }).text("Product reports: " + product.product_name)
//                                )
//                        )
//                    });
//                }
//
//                if (response.categories.length == 0 && response.products.length == 0) {
//                    $(".report-list-container .file-tree").append(
//                            $("<li>").text("No reports in the list.")
//                    )
//                }
//            });


            $("a[data-toggle=tab][href='#report-history']").on("shown.bs.tab", function (e) {
                if (tblReport == null) {
                    initTblReport();
                }
            });


        });

        function initTblReport() {
            tblReport = $("#tbl-report").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "pageLength": 25,
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'<\"toolbar-bottom-left\">><'col-sm-7'p>>",
                "language": {
                    "emptyTable": "No reports in the list",
                    "zeroRecords": "No reports in the list"
                },
                "ajax": {
                    "url": "{{route('report.index')}}",
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
                        "name": "file_name",
                        "data": function (data) {
                            var createdAt = timestampToDateTimeByFormat(moment(data.created_at).unix(), 'Ymd');
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": data.urls.show,
                                        "download": "download"
                                    }).text(createdAt + '_' + data.file_name + "." + data.file_type)
                            ).html()
                        }
                    },
                    {
                        "name": "report_owner_type",
                        "data": function (data) {
                            var reportOwnerName = "";
                            if (data.report_owner_type == "category") {
                                reportOwnerName = data.report_owner.category_name;
                            } else {
                                reportOwnerName = data.report_owner.product_name;
                            }
                            return data.report_owner_type + ' - ' + reportOwnerName;
                        }
                    },
                    {
                        "name": "created_at",
                        "data": function (data) {
                            return timestampToDateTimeByFormat(moment(data.created_at).unix(), datefmt + " " + timefmt);
                        }
                    },
                    {
                        "sortable": false,
                        "data": function (data) {
                            console.info(data);
                            return $("<div>").append(
                                    $("<div>").addClass("text-center").append(
                                            $("<a>").addClass("text-muted").attr({
                                                "href": data.urls.show
                                            }).append(
                                                    $("<i>").addClass("glyphicon glyphicon-download-alt")
                                            ),
                                            "&nbsp;&nbsp;",
                                            $("<a>").addClass("text-muted").attr({
                                                "href": "#",
                                                "data-type": data.report_owner_type,
                                                "data-id": data.report_id,
                                                "data-delete-url": data.urls.delete,
                                                "onclick": "deleteReport(this, function(){tblReport.ajax.reload();}); return false;"
                                            }).append(
                                                    $("<i>").addClass("glyphicon glyphicon-trash text-danger")
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
        }

        function initialisePopover() {
            $("[data-toggle=popover]").popover();
        }

        {{--function toggleCategoryFolder(el) {--}}
        {{--var $li = $(el).closest("li");--}}
        {{--if ($li.hasClass("collapsed")) {--}}
        {{--showReportListLoading();--}}
        {{--$li.removeClass("collapsed").addClass("expanded");--}}
        {{--var categoryId = $(el).attr("data-category-id");--}}
        {{--$.ajax({--}}
        {{--"url": "{{route('report.index')}}",--}}
        {{--"method": "get",--}}
        {{--"dataType": "json",--}}
        {{--"data": {--}}
        {{--"category_id": categoryId--}}
        {{--},--}}
        {{--"success": function (response) {--}}
        {{--hideReportListLoading();--}}
        {{--if (response.status == true) {--}}
        {{--var $ul = $("<ul>").addClass("file-tree").hide();--}}

        {{--$.each(response.reports, function (index, report) {--}}
        {{--var ext = "xlsx";--}}
        {{--switch (report.file_type) {--}}
        {{--case "pdf":--}}
        {{--ext = "pdf";--}}
        {{--break;--}}
        {{--case "xls":--}}
        {{--case "xlsx":--}}
        {{--default:--}}
        {{--ext = "xls";--}}
        {{--}--}}
        {{--$ul.append(--}}
        {{--$("<li>").addClass("file ext_" + ext).append(--}}
        {{--$("<a>").addClass("file-anchor").attr({--}}
        {{--"data-delete-url": report.urls["delete"],--}}
        {{--"data-report-id": report.report_id,--}}
        {{--"href": report.urls['show'],--}}
        {{--"download": "download",--}}
        {{--"title": moment(report.created_at).format("YYYYMMDD") + "_" + report.file_name + "." + report.file_type--}}
        {{--}).text(moment(report.created_at).format("YYYYMMDD") + "_" + report.file_name + "." + report.file_type)--}}
        {{--)--}}
        {{--)--}}
        {{--});--}}
        {{--$(el).after($ul);--}}
        {{--$ul.slideDown();--}}
        {{--}--}}
        {{--},--}}
        {{--"error": function (xhr, status, error) {--}}
        {{--hideReportListLoading();--}}
        {{--describeServerRespondedError(xhr.status);--}}
        {{--}--}}
        {{--})--}}
        {{--} else {--}}
        {{--$li.addClass("collapsed").removeClass("expanded");--}}
        {{--$li.find("ul").slideUp(function () {--}}
        {{--$(this).remove();--}}
        {{--});--}}
        {{--}--}}
        {{--}--}}

        {{--function toggleProductFolder(el) {--}}
        {{--var $li = $(el).closest("li");--}}
        {{--if ($li.hasClass("collapsed")) {--}}
        {{--showReportListLoading();--}}
        {{--$li.removeClass("collapsed").addClass("expanded");--}}
        {{--var productId = $(el).attr("data-product-id");--}}
        {{--$.ajax({--}}
        {{--"url": "{{route('report.index')}}",--}}
        {{--"method": "get",--}}
        {{--"dataType": "json",--}}
        {{--"data": {--}}
        {{--"product_id": productId--}}
        {{--},--}}
        {{--"success": function (response) {--}}
        {{--hideReportListLoading();--}}
        {{--if (response.status == true) {--}}
        {{--var $ul = $("<ul>").addClass("file-tree").hide();--}}

        {{--$.each(response.reports, function (index, report) {--}}
        {{--var ext = "xlsx";--}}
        {{--switch (report.file_type) {--}}
        {{--case "pdf":--}}
        {{--ext = "pdf";--}}
        {{--break;--}}
        {{--case "xls":--}}
        {{--case "xlsx":--}}
        {{--default:--}}
        {{--ext = "xls";--}}
        {{--}--}}
        {{--$ul.append(--}}
        {{--$("<li>").addClass("file ext_" + ext).append(--}}
        {{--$("<a>").addClass("file-anchor").attr({--}}
        {{--"data-delete-url": report.urls["delete"],--}}
        {{--"data-report-id": report.report_id,--}}
        {{--"href": report.urls['show'],--}}
        {{--"download": "download",--}}
        {{--"title": moment(report.created_at).format("YYYYMMDD") + "_" + report.file_name + "." + report.file_type--}}
        {{--}).text(moment(report.created_at).format("YYYYMMDD") + "_" + report.file_name + "." + report.file_type)--}}
        {{--)--}}
        {{--)--}}
        {{--});--}}
        {{--$(el).after($ul);--}}
        {{--$ul.slideDown();--}}
        {{--}--}}
        {{--},--}}
        {{--"error": function (xhr, status, error) {--}}
        {{--hideReportListLoading();--}}
        {{--describeServerRespondedError(xhr.status);--}}
        {{--}--}}
        {{--})--}}
        {{--} else {--}}
        {{--$li.addClass("collapsed").removeClass("expanded");--}}
        {{--$li.find("ul").slideUp(function () {--}}
        {{--$(this).remove();--}}
        {{--});--}}
        {{--}--}}
        {{--}--}}


        {{--function loadCategoriesAndProductsWithReports(callback) {--}}
        {{--showReportListLoading();--}}
        {{--$.ajax({--}}
        {{--"url": "{{route('report.index')}}",--}}
        {{--"method": "get",--}}
        {{--"dataType": "json",--}}
        {{--"success": function (response) {--}}
        {{--hideReportListLoading();--}}
        {{--if ($.isFunction(callback)) {--}}
        {{--callback(response);--}}
        {{--}--}}
        {{--},--}}
        {{--"error": function (xhr, status, error) {--}}
        {{--hideReportListLoading();--}}
        {{--describeServerRespondedError(xhr.status);--}}
        {{--}--}}
        {{--})--}}
        {{--}--}}

        {{--function showReportListLoading() {--}}
        {{--$(".report-list-loading").fadeIn();--}}
        {{--}--}}

        {{--function hideReportListLoading() {--}}
        {{--$(".report-list-loading").fadeOut();--}}
        {{--}--}}


        function showReportTaskForm(el) {
            showLoading();
            $.ajax({
                "url": $(el).attr("data-url"),
                "method": "get",
                "success": function (html) {
                    gaEditReportFromReportsPage({
                        "Type": $(el).attr("data-report-type")
                    });

                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "updateCallback": function (response) {
                                    tblReportTask.ajax.reload();
                                },
                                "deleteCallback": function (response) {
                                    tblReportTask.ajax.reload();
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $("#modal-report-task-product").remove();
                        $("#modal-report-task-category").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }

        function deleteReportTask(el) {
            deletePopup("Delete Report Schedule", "Are you sure you want to delete this " + capitalise($(el).attr("data-report-type")) + " Report?",
                    "By deleting this " + capitalise($(el).attr("data-report-type")) + " Report, you will lose the following:",
                    [
                        "Future Reports scheduled for this " + capitalise($(el).attr("data-report-type")) + " based on frequency, time and date previously set"
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
                                            gaDeleteReportFromReportsPage({
                                                "Type": $(el).attr("data-report-type")
                                            });

                                            tblReportTask.row($(el).closest("tr")).remove().draw();
                                        } else {
                                            alertP("Oops! Something went wrong.", "Unable to delete report schedule, please try again later.");
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

        function deleteReport(el, callback) {
            deletePopup("Delete Report", "Are you sure you want to delete this " + capitalise($(el).attr("data-type")) + " Report?",
                    "By deleting this " + capitalise($(el).attr("data-type")) + " Report, you will lose the following:",
                    [
                        "All historical data related to this " + capitalise($(el).attr("data-type")) + " Report"
                    ],
                    {
                        "affirmative": {
                            "text": "DELETE",
                            "class": "btn-danger btn-flat",
                            "dismiss": true,
                            "callback": function () {
                                showLoading();
                                $.ajax({
                                    "url": $(el).attr("data-delete-url"),
                                    "method": "delete",
                                    "dataType": "json",
                                    "success": function (response) {
                                        hideLoading();
                                        if (response.status == true) {
                                            if ($.isFunction(callback)) {
                                                callback(response);
                                            }
                                        } else {
                                            alertP("Oops! Something went wrong.", "Unable to delete report, please try again later.");
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