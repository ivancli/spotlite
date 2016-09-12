@extends('layouts.adminlte')
@section('title', 'Crawler - Site Management')
@section('header_title', 'Crawler - Site Management')
@section('breadcrumbs')
    {!! Breadcrumbs::render('admin_product_site') !!}
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
                        "name": "product_site_id",
                        "data": "product_site_id"
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
                        "name": "sites.site_url",
                        "data": function (data) {
                            return getDomainFromURL(data.site_url);
                        }
                    },
                    {
                        "name": "sites.site_url",
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
//                            return stripDomainFromURL(data.site_url);
                        }
                    },
                    {
                        "name": "sites.site_xpath",
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<div>").css("padding-right", "20px").append(
                                            $("<span>").text(data.site_xpath),
                                            $("<a>").attr({
                                                "href": "#"
                                            }).append(
                                                    $("<i>").addClass("fa fa-pencil float-right text-muted").css("margin-right", "-20px")
                                            )
                                    )
                            ).html();

//                            return data.site_xpath;
                        }
                    },
                    {
                        "name": "sites.recent_price",
                        "data": function (data) {
                            return data.recent_price;
                        }
                    },
                    {
                        "name": "sites.last_crawled_at",
                        "data": function (data) {
                            return data.last_crawled_at;
                        }
                    },
                    {
                        "name": "site.status",
                        "sortable": false,
                        "data": function () {
                            return ""
                        }
                    },
                    {
                        "class": "text-center",
                        "sortable": false,
                        "data": function (data) {
                            console.info(data);
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": data.site_url,
                                        "target": "_blank"
                                    }).append(
                                            $("<i>").addClass("fa fa-globe")
                                    ).addClass("text-muted"),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#"
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
    </script>
@stop