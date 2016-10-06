@extends('layouts.adminlte')
@section('title', 'Reports')
@section('header_title', "Reports")

@section('breadcrumbs')
    {!! Breadcrumbs::render('report_index') !!}
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Report Schedule</h3>
                </div>
                <div class="box-body">
                    {{--TODO put real content here--}}
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
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-solid">
                <div class="overlay report-list-loading">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
                <div class="box-header with-border">
                    <h3 class="box-title">Historical Reports</h3>
                </div>
                <div class="box-body">
                    <div class="row m-b-10">
                        <div class="col-sm-12">
                            <div class="report-list-container">
                                <ul class="file-tree">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        var tblReportTask = null;
        $(function () {
            $.contextMenu({
                "selector": '.report-list-container .file-anchor',
                "items": {
                    "download": {
                        "name": "Download",
                        "callback": function (key, opt) {
                            var el = opt.$trigger.context;
                            el.click();
                        }
                    },
                    "delete": {
                        "name": "Delete",
                        "callback": function (key, opt) {
                            var el = opt.$trigger.context;
                            deleteReport(el, function (response) {
                                $(el).closest("li").remove();
                            });

                        }
                    }
                }
            });


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
                                                                $("<strong>").text(data.report_task_owner.productSiteCount)
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
                                                                $("<strong>").text(data.report_task_owner.productSiteCount)
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
                                return moment(data.last_sent_at).format("lll")
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
                                                "onclick": "showReportTaskForm(this)"
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


            loadCategoriesAndProductsWithReports(function (response) {
                if (typeof response.categories != "undefined") {
                    $.each(response.categories, function (index, category) {
                        $(".report-list-container .file-tree").append(
                                $("<li>").addClass("directory collapsed").append(
                                        $("<a>").attr({
                                            "data-category-id": category.category_id,
                                            "href": "#",
                                            "onclick": "toggleCategoryFolder(this); return false;"
                                        }).text("Category reports: " + category.category_name)
                                )
                        )
                    });
                }
                if (typeof response.products != "undefined") {
                    $.each(response.products, function (index, product) {
                        $(".report-list-container .file-tree").append(
                                $("<li>").addClass("directory collapsed").append(
                                        $("<a>").attr({
                                            "data-product-id": product.product_id,
                                            "href": "#",
                                            "onclick": "toggleProductFolder(this); return false;"
                                        }).text("Product reports: " + product.product_name)
                                )
                        )
                    });
                }

                if (response.categories.length == 0 && response.products.length == 0) {
                    $(".report-list-container .file-tree").append(
                            $("<li>").text("No reports in the list.")
                    )
                }
            });

        });
        function initialisePopover() {
            $("[data-toggle=popover]").popover();
        }

        function toggleCategoryFolder(el) {
            var $li = $(el).closest("li");
            if ($li.hasClass("collapsed")) {
                showReportListLoading();
                $li.removeClass("collapsed").addClass("expanded");
                var categoryId = $(el).attr("data-category-id");
                $.ajax({
                    "url": "{{route('report.index')}}",
                    "method": "get",
                    "dataType": "json",
                    "data": {
                        "category_id": categoryId
                    },
                    "success": function (response) {
                        hideReportListLoading();
                        if (response.status == true) {
                            var $ul = $("<ul>").addClass("file-tree").hide();

                            $.each(response.reports, function (index, report) {
                                var ext = "xlsx";
                                switch (report.file_type) {
                                    case "pdf":
                                        ext = "pdf";
                                        break;
                                    case "xls":
                                    case "xlsx":
                                    default:
                                        ext = "xls";
                                }
                                $ul.append(
                                        $("<li>").addClass("file ext_" + ext).append(
                                                $("<a>").addClass("file-anchor").attr({
                                                    "data-delete-url": report.urls["delete"],
                                                    "data-report-id": report.report_id,
                                                    "href": report.urls['show'],
                                                    "download": "download",
                                                    "title": moment(report.created_at).format("YYYYMMDD") + "_" + report.file_name + "." + report.file_type
                                                }).text(moment(report.created_at).format("YYYYMMDD") + "_" + report.file_name + "." + report.file_type)
                                        )
                                )
                            });
                            $(el).after($ul);
                            $ul.slideDown();
                        }
                    },
                    "error": function (xhr, status, error) {
                        hideReportListLoading();
                    }
                })
            } else {
                $li.addClass("collapsed").removeClass("expanded");
                $li.find("ul").slideUp(function () {
                    $(this).remove();
                });
            }
        }

        function toggleProductFolder(el) {
            var $li = $(el).closest("li");
            if ($li.hasClass("collapsed")) {
                showReportListLoading();
                $li.removeClass("collapsed").addClass("expanded");
                var productId = $(el).attr("data-product-id");
                $.ajax({
                    "url": "{{route('report.index')}}",
                    "method": "get",
                    "dataType": "json",
                    "data": {
                        "product_id": productId
                    },
                    "success": function (response) {
                        hideReportListLoading();
                        if (response.status == true) {
                            var $ul = $("<ul>").addClass("file-tree").hide();

                            $.each(response.reports, function (index, report) {
                                var ext = "xlsx";
                                switch (report.file_type) {
                                    case "pdf":
                                        ext = "pdf";
                                        break;
                                    case "xls":
                                    case "xlsx":
                                    default:
                                        ext = "xls";
                                }
                                $ul.append(
                                        $("<li>").addClass("file ext_" + ext).append(
                                                $("<a>").addClass("file-anchor").attr({
                                                    "data-delete-url": report.urls["delete"],
                                                    "data-report-id": report.report_id,
                                                    "href": report.urls['show'],
                                                    "download": "download",
                                                    "title": moment(report.created_at).format("YYYYMMDD") + "_" + report.file_name + "." + report.file_type
                                                }).text(moment(report.created_at).format("YYYYMMDD") + "_" + report.file_name + "." + report.file_type)
                                        )
                                )
                            });
                            $(el).after($ul);
                            $ul.slideDown();
                        }
                    },
                    "error": function (xhr, status, error) {
                        hideReportListLoading();
                    }
                })
            } else {
                $li.addClass("collapsed").removeClass("expanded");
                $li.find("ul").slideUp(function () {
                    $(this).remove();
                });
            }
        }


        function loadCategoriesAndProductsWithReports(callback) {
            showReportListLoading();
            $.ajax({
                "url": "{{route('report.index')}}",
                "method": "get",
                "dataType": "json",
                "success": function (response) {
                    hideReportListLoading();
                    if ($.isFunction(callback)) {
                        callback(response);
                    }
                },
                "error": function (xhr, status, error) {
                    hideReportListLoading();
                }
            })
        }

        function showReportListLoading() {
            $(".report-list-loading").fadeIn();
        }

        function hideReportListLoading() {
            $(".report-list-loading").fadeOut();
        }


        function showReportTaskForm(el) {
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
                    alertP("Error", "Unable to show edit report form, please try again later.");
                }
            });
        }

        function deleteReportTask(el) {
            confirmP("Delete Report Schedule", "Do you want to delete this schedule?", {
                "affirmative": {
                    "text": "Delete",
                    "class": "btn-danger",
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
                                    tblReportTask.row($(el).closest("tr")).remove().draw();
                                } else {
                                    alertP("Error", "Unable to delete report schedule, please try again later.");
                                }
                            },
                            "error": function (xhr, status, error) {
                                hideLoading();
                                alertP("Error", "Unable to delete report schedule, please try again later.");
                            }
                        })
                    }
                },
                "negative": {
                    "text": "Cancel",
                    "class": "btn-default",
                    "dismiss": true
                }
            })
        }

        function deleteReport(el, callback) {
            confirmP("Delete Report", "Do you want to delete this report?", {
                "affirmative": {
                    "text": "Delete",
                    "class": "btn-danger",
                    "dismiss": true,
                    "callback": function () {
                        console.info($(el).attr("data-delete-url"));
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
                                    alertP("Error", "Unable to delete report, please try again later.");
                                }
                            },
                            "error": function (xhr, status, error) {
                                hideLoading();
                                alertP("Error", "Unable to delete report, please try again later.");
                            }
                        })
                    }
                },
                "negative": {
                    "text": "Cancel",
                    "class": "btn-default",
                    "dismiss": true
                }
            })
        }
    </script>
@stop