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
                <div class="box-body toolbar">
                    <div class="form-inline">
                        Filter by status &nbsp;
                        <select id="sel-status-filter" class="form-control sl-form-control"
                                onchange="reloadSiteTable();">
                            <option value=""></option>
                            <option value="ok">OK</option>
                            <option value="fail_html">No HTML</option>
                            <option value="fail_price">Incorrect Price Format</option>
                            <option value="fail_xpath">Incorrect xPath</option>
                            <option value="null_xpath">No xPath</option>
                            <option value="waiting">Waiting</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table id="tbl-site" class="table table-bordered table-hover table-striped">
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
                            <th width="100"></th>
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
        var tblSite = null;
        $(function () {
            jQuery.fn.dataTable.Api.register('processing()', function (show) {
                return this.iterator('table', function (ctx) {
                    ctx.oApi._fnProcessingDisplay(ctx, show);
                });
            });
            tblSite = $("#tbl-site").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "pageLength": 25,
                "order": [[0, "asc"]],
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'<\"toolbar-bottom-left\">><'col-sm-7'p>>",
                "ajax": {
                    "url": "{{route(request()->route()->getName())}}",
                    "data": function (d) {
                        if ($("#sel-status-filter").val() != "") {
                            var statusSearch = $("#sel-status-filter").val();
                            d.status = statusSearch;
                        }

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
                                        }).addClass("text-right")
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
                        "class": "break-word",
                        "name": "site_xpath",
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<div>").addClass("xpath-wrapper").append(
                                            $("<div>").append(
                                                    $("<span>").text(data.site_xpath).addClass("lbl-site-xpath"),
                                                    $("<input>").attr({
                                                        "type": "text",
                                                        "onkeyup": "if(event.keyCode == 27) togglexPathInput(this); if(event.keyCode == 13) togglexPathInput($(this).closest('.xpath-wrapper').find('[data-url]').get(0));  return false;",
                                                        "value": data.site_xpath
                                                    }).hide().addClass("txt-site-xpath form-control input-sm")
                                            ),
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "togglexPathInput(this); return false;",
                                                "data-url": data.urls.admin_update
                                            }).append(
                                                    $("<i>").addClass("fa fa-pencil text-muted")
                                            ).addClass("btn-edit-xpath")
                                    )
                            ).html();

//                            return data.site_xpath;
                        }
                    },
                    {
                        "name": "recent_price",
                        "data": function (data) {
                            if (data.recent_price != null) {
                                return $("<div>").append(
                                        $("<div>").append(
                                                "$" + parseFloat(data.recent_price).formatMoney()
                                        ).addClass("text-right")
                                ).html();
                            }
                            return data.recent_price;
                        }
                    },
                    {
                        "name": "last_crawled_at",
                        "data": function (data) {
                            //data.last_crawled_at;
                            return $("<div>").append(
                                    $("<div>").append(
                                            data.last_crawled_at
                                    ).addClass("text-right")
                            ).html();
                        }
                    },
                    {
                        "class": "text-center",
                        "name": "status",
                        "sortable": false,
                        "data": function (data) {
                            var $text = $("<div>");
                            switch (data.status) {
                                case "ok":
                                    $text.append(
                                            $("<i>").addClass("text-success fa fa-check")
                                    );
                                    break;
                                case "fail_html":
                                    $text.append(
                                            $("<i>").addClass("text-danger fa fa-times"),
                                            "&nbsp;",
                                            "HTML"
                                    ).attr({
                                        "title": "The site/web page is not accessible with current crawler class.",
                                        "data-toggle": "tooltip"
                                    });
                                    break;
                                case "fail_price":
                                    $text.append(
                                            $("<i>").addClass("text-danger fa fa-times"),
                                            "&nbsp;",
                                            "Price"
                                    ).attr({
                                        "title": "The price is not in a correct format, problem might be from the incorrect xPath.",
                                        "data-toggle": "tooltip"
                                    });
                                    break;
                                case "fail_xpath":
                                    $text.append(
                                            $("<i>").addClass("text-danger fa fa-times"),
                                            "&nbsp;",
                                            "xPath"
                                    ).attr({
                                        "title": "xPath is pointing to unknown elements which cannot be fetched from HTML code.",
                                        "data-toggle": "tooltip"
                                    });
                                    break;
                                case "null_xpath":
                                    $text.append(
                                            $("<i>").addClass("text-warning fa fa-question"),
                                            "&nbsp;",
                                            "xPath"
                                    ).attr({
                                        "title": "xPath is not yet defined.",
                                        "data-toggle": "tooltip"
                                    });
                                    break;
                                case "waiting":
                                    $text.append(
                                            $("<i>").addClass("text-muted fa fa-clock-o")
                                    ).attr({
                                        "title": "Waiting for crawler to trigger.",
                                        "data-toggle": "tooltip"
                                    });
                                    break;
                                default:
                            }
                            var $output = $("<div>").append(
                                    $text
                            );
                            return $output.html();
                        }
                    },
                    {
                        "class": "text-center",
                        "sortable": false,
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": data.site_url,
                                        "target": "_blank",
                                        "title": "Go to the site",
                                        "data-toggle": "tooltip"
                                    }).append(
                                            $("<i>").addClass("glyphicon glyphicon-globe")
                                    ).addClass("text-muted"),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#",
                                        "onclick": 'showEditCrawlerForm(this); return false;',
                                        "title": "Edit crawler",
                                        "data-toggle": "tooltip",
                                        "data-url": data.urls.admin_crawler_edit
                                    }).append(
                                            $("<i>").addClass("glyphicon glyphicon-cog")
                                    ).addClass("text-muted"),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#",
                                        "onclick": 'testCrawler(this); return false;',
                                        "data-url": data.urls.test,
                                        "title": "Test crawl",
                                        "data-toggle": "tooltip"
                                    }).append(
                                            $("<i>").addClass("glyphicon glyphicon-refresh")
                                    ).addClass("text-muted"),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#",
                                        "data-url": data.urls.admin_delete,
                                        "onclick": "btnDeleteSiteOnClick(this); return false;",
                                        "title": "Delete this site",
                                        "data-toggle": "tooltip"
                                    }).append(
                                            $("<i>").addClass("glyphicon glyphicon-trash")
                                    ).addClass("text-muted text-danger")
                            ).html()
                        }
                    }
                ]
            });

            $(".toolbar-bottom-left").append(
                    $("<a>").attr({
                        "href": "#",
                        "onclick": "showAddSiteForm(); return false;"
                    }).addClass("btn btn-default").text("Add Site")
            )
        });

        function togglexPathInput(el) {
            var $txt = $(el).closest("tr").find(".txt-site-xpath");
            var $lbl = $(el).closest("tr").find(".lbl-site-xpath");
            if ($lbl.is(":visible")) {
                $lbl.hide();
                $txt.show().focus();
            } else {
                if ($(el).attr("data-url")) {
                    /* TODO save xpath */
                    updateXPath($(el).attr("data-url"), {"site_xpath": $txt.val()}, function (response) {
                        $lbl.show().text(response.site.site_xpath);
                        $txt.hide().val(response.site.site_xpath);
                    }, function (response) {

                    });
                } else {
                    $lbl.show();
                    $txt.hide().val($lbl.text());
                }
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
                    if (response.status == true) {
                        alertP("Crawler Test", "The crawled price is $" + response.price);
                    } else {
                        if (typeof response.errors != "undefined") {
                            alertP("Error", response.errors.join(" "));
                        } else {
                            alertP("Error", "Unable to test the crawler, please try again later.");
                        }
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to test the crawler, please try again later.");
                }
            })
        }

        function showAddSiteForm() {
            showLoading();
            $.get("{{route("admin.site.create")}}", function (html) {
                hideLoading();
                var $modal = $(html);
                $modal.modal();
                $modal.on("shown.bs.modal", function () {
                    if ($.isFunction(modalReady)) {
                        modalReady({
                            "callback": function (response) {
                                tblSite.ajax.reload();
                            }
                        })
                    }
                });
                $modal.on("hidden.bs.modal", function () {
                    $(this).remove();
                });
            })
        }

        function showEditCrawlerForm(el) {
            showLoading();
            $.get($(el).attr("data-url"), function (html) {
                hideLoading();
                var $modal = $(html);
                $modal.modal();
                $modal.on("shown.bs.modal", function () {
                    if ($.isFunction(modalReady)) {
                        modalReady({
                            "callback": function (response) {
                                tblSite.ajax.reload();
                            }
                        })
                    }
                });
                $modal.on("hidden.bs.modal", function () {
                    $(this).remove();
                });
            })
        }


        function btnDeleteSiteOnClick(el) {
            confirmP("Delete site", "Do you want to delete this site?", {
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
                                    alertP("Delete domain", "The domain has been deleted.");
                                    $(el).closest(".site-wrapper").remove();
                                    tblDomain.row($(el).closest("tr")).remove().draw();
                                } else {
                                    alertP("Error", "Unable to delete domain, please try again later.");
                                }
                            },
                            "error": function (xhr, status, error) {
                                hideLoading();
                                alertP("Error", "Unable to delete domain, please try again later.");
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

        function reloadSiteTable() {
            tblSite.ajax.reload();
        }
    </script>
@stop