@extends('layouts.adminlte')
@section('title', 'Crawler - Site Management')
@section('header_title', 'Crawler - Site Management')
@section('breadcrumbs')
    {!! Breadcrumbs::render('admin_site') !!}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table id="tbl-product-site" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th class="shrink">ID</th>
                            <th>Created at</th>
                            <th>Site</th>
                            <th width="200">URL</th>
                            <th width="200">xPath</th>
                            <th>Last Price</th>
                            <th>Last Crawl</th>
                            <th>Status</th>
                            <th width="70"></th>
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
        var tblProductSite = null;
        $(function () {
            jQuery.fn.dataTable.Api.register('processing()', function (show) {
                return this.iterator('table', function (ctx) {
                    ctx.oApi._fnProcessingDisplay(ctx, show);
                });
            });
            tblProductSite = $("#tbl-product-site").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "pageLength": 25,
                "order": [[0, "asc"]],
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'><'col-sm-7'p>>",
                "ajax": {
                    "url": "{{route(request()->route()->getName())}}",
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
                        "name": "site_id",
                        "data": "site_id"
                    },
                    {
                        "name": "created_at",
                        "data": function (data) {
                            if (data.created_at != null) {
                                var timestamp = strtotime(data.created_at)
                                return $("<div>").append(
                                        $("<div>").text(timestampToDateTimeByFormat(timestamp, "Y-m-d")).attr({
                                            "title": timestampToDateTimeByFormat(timestamp, "Y-m-d H:i"),
                                            "data-toggle": "tooltip"
                                        })
                                ).html();
                            } else {
                                return "";
                            }
                        }
                    },
                    {
                        "name": "site_url",
                        "data": function (data) {
                            return getDomainFromURL(data.site_url);
                        }
                    },
                    {
                        "name": "site_url",
                        "data": function (data) {
                            var url = stripDomainFromURL(data.site_url);
                            url = url.length > 30 ? url.substr(0, 30) + '…' : url;
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": data.site_url,
                                        "title": data.site_url,
                                        "target": "_blank",
                                        "data-toggle": "tooltip"
                                    }).text(url).addClass("text-muted")
                            ).html();
                        }
                    },
                    {
                        "name": "site_xpath",
                        "data": function (data) {
                            console.info(data);
                            return $("<div>").append(
                                    $("<div>").css("padding-right", "20px").append(
                                            $("<span>").text(data.site_xpath).addClass("lbl-site-xpath"),
                                            $("<input>").attr({
                                                "type": "text",
                                                "value": data.site_xpath
                                            }).hide().addClass("txt-site-xpath form-control input-sm"),
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "togglexPathInput(this); return false;",
                                                "data-url": data.urls.admin_update
                                            }).append(
                                                    $("<i>").addClass("fa fa-pencil float-right text-muted").css("margin-right", "-20px")
                                            )
                                    )
                            ).html();

//                            return data.site_xpath;
                        }
                    },
                    {
                        "name": "recent_price",
                        "data": function (data) {
                            return data.recent_price;
                        }
                    },
                    {
                        "name": "last_crawled_at",
                        "data": function (data) {
                            return data.last_crawled_at;
                        }
                    },
                    {
                        "name": "status",
                        "sortable": false,
                        "data": function () {
                            return ""
                        }
                    },
                    {
                        "class": "text-center",
                        "sortable": false,
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": data.site_url,
                                        "target": "_blank"
                                    }).append(
                                            $("<i>").addClass("fa fa-globe")
                                    ).addClass("text-muted"),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#",
                                        "onclick": 'testCrawler(this); return false;',
                                        "data-url": data.urls.test
                                    }).append(
                                            $("<i>").addClass("fa fa-refresh")
                                    ).addClass("text-muted"),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#"
                                    }).append(
                                            $("<i>").addClass("fa fa-trash-o")
                                    ).addClass("text-muted text-danger")
                            ).html()
                        }
                    }
                ]
            });
        });

        function togglexPathInput(el) {
            var $txt = $(el).closest("tr").find(".txt-site-xpath");
            var $lbl = $(el).closest("tr").find(".lbl-site-xpath");
            if ($lbl.is(":visible")) {
                $lbl.hide();
                $txt.show();
            } else {
                /* TODO save xpath */
                updateXPath($(el).attr("data-url"), {"site_xpath": $txt.val()}, function (response) {
                    console.info('response', response);
                    $lbl.show().text(response.site.site_xpath);
                    $txt.hide().val(response.site.site_xpath);
                }, function (response) {

                });
            }
        }

        function updateXPath(url, data, successCallback, errorCallback) {
            showLoading();
            $.ajax({
                "url": url,
                "method": "put",
                "data": data,
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        if ($.isFunction(successCallback)) {
                            successCallback(response);
                        }
                    } else {
                        if ($.isFunction(errorCallback)) {
                            errorCallback(response);
                        }
                        alertP("Error", "unable to update xpath, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    if ($.isFunction(errorCallback)) {
                        errorCallback(response);
                    }
                    alertP("Error", "unable to update xpath, please try again later.");
                }
            })
        }

        function testCrawler(el) {
            showLoading();
            $.ajax({
                "url": $(el).attr("data-url"),
                "method": "post",
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    console.info(response);
                    if (response.status == true) {

                    } else {
                        alertP("Error", "Unable to test the crawler, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to test the crawler, please try again later.");
                }
            })
        }
    </script>
@stop