@extends('layouts.adminlte')
@section('title', 'Crawler - Site Management')
@section('header_title', 'Crawler - Site Management')
@section('breadcrumbs')
{{--    {!! Breadcrumbs::render('admin_site') !!}--}}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body toolbar">
                    <div class="form-inline">
                        Filter by status &nbsp;
                        <select id="sel-status-filter" class="form-control sl-form-control" multiple="multiple"
                                onchange="reloadSiteTable();">
                            <option value="ok">OK</option>
                            <option value="fail_html">No HTML</option>
                            <option value="fail_price">Incorrect Price Format</option>
                            <option value="fail_xpath">Incorrect xPath</option>
                            <option value="null_xpath">No xPath</option>
                            <option value="waiting">Waiting</option>
                            <option value="sample">Sample</option>
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
                    <table id="tbl-site" class="table table-bordered table-hover table-striped site-wrapper">
                        <thead>
                        <tr>
                            <th class="shrink">ID</th>
                            <th>Site</th>
                            <th width="200">URL</th>
                            <th width="200">xPath</th>
                            <th>Last Price</th>
                            <th>Last Crawl</th>
                            <th>Created at</th>
                            <th>Status</th>
                            <th>Comment</th>
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
            $("#sel-status-filter").select2();

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
                "language": {
                    "emptyTable": "No sites in the list",
                    "zeroRecords": "No sites in the list"
                },
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
                        "name": "site_url",
                        "data": function (data) {
                            return getDomainFromURL(data.site_url);
                        }
                    },
                    {
                        "class": "site-url",
                        "name": "site_url",
                        "data": function (data) {
                            var url = stripDomainFromURL(data.site_url);
                            url = url.length > 30 ? url.substr(0, 30) + '…' : url;
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": data.site_url,
                                        "data-content": data.site_url,
                                        "data-trigger": "hover",
                                        "target": "_blank",
                                        "data-toggle": "popover"
                                    }).text(url).addClass("text-muted")
                            ).html();
                        }
                    },
                    {
                        "class": "break-word",
                        "name": "site_xpath",
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<div>").addClass("xpath-wrapper").css("min-width", "100px").append(
                                            $("<div>").append(
                                                    $("<span>").text(data.preference != null ? data.preference.xpath_1 : '').addClass("lbl-site-xpath"),
                                                    $("<input>").attr({
                                                        "type": "text",
                                                        "onkeyup": "if(event.keyCode == 27) togglexPathInput(this); if(event.keyCode == 13) togglexPathInput($(this).closest('.xpath-wrapper').find('[data-url]').get(0));  return false;",
                                                        "value": data.preference != null ? data.preference.xpath_1 : ''
                                                    }).hide().addClass("txt-site-xpath form-control input-sm")
                                            ),
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "showEditxPathForm(this); return false;",
                                                "data-url": data.urls.admin_xpath_edit
                                            }).append(
                                                    $("<i>").addClass("fa fa-pencil text-muted")
                                            ).addClass("btn-edit-xpath btn-flat")
                                    )
                            ).html();
                        }
                    },
                    {
                        "name": "recent_price",
                        "data": function (data) {
                            if (data.recent_price != null) {
                                return $("<div>").append(
                                        $("<div>").append(
                                                "$" + parseFloat(data.recent_price).formatMoney()
                                        )
                                ).html();
                            }
                            return data.recent_price;
                        }
                    },
                    {
                        "name": "last_crawled_at",
                        "data": function (data) {
                            if (data.last_crawled_at != null) {
                                return timestampToDateTimeByFormat(moment(data.last_crawled_at).unix(), datefmt + " " + timefmt)
                            } else {
                                return null;
                            }
                        }
                    },
                    {
                        "name": "created_at",
                        "data": function (data) {
                            if (data.created_at != null) {
                                return $("<div>").append(
                                        $("<span>").text(timestampToDateTimeByFormat(moment(data.created_at).unix(), datefmt)).attr({
                                            "title": timestampToDateTimeByFormat(moment(data.created_at).unix(), datefmt + ' ' + timefmt),
                                            "data-toggle": "tooltip"
                                        })
                                ).html();
                            } else {
                                return "";
                            }
                        }
                    },
                    {
                        "class": "text-center",
                        "name": "status",
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
                                case "invalid":
                                    $text.append(
                                            $("<i>").addClass("text-danger fa fa-ban")
                                    ).attr({
                                        "title": "The site is marked as invalid",
                                        "data-toggle": "tooltip"
                                    });
                                    break;
                                case "sample":
                                    $text.append(
                                            $("<i>").addClass("text-warning fa fa-tag")
                                    ).attr({
                                        "title": "This is a sample site",
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
                        "name": "comment",
                        "data": "comment",
                        "sortable": false
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
                                    "&nbsp;&nbsp;",
                                    $("<div>").addClass("btn-group").attr({
                                        "data-toggle": "tooltip",
                                        "title": "Update status"
                                    }).append(
                                            $("<a>").addClass("dropdown-toggle text-muted").attr({
                                                "data-toggle": "dropdown",
                                                "href": "#"
                                            }).append(
                                                    $("<i>").addClass("glyphicon glyphicon-tags")
                                            ),
                                            $("<ul>").addClass("dropdown-menu").attr("role", "menu").append(
                                                    $("<li>").append(
                                                            $("<a>").attr({
                                                                "href": "#",
                                                                "onclick": "setSiteStatus(this, 'invalid'); return false;",
                                                                "data-url": data.urls.admin_status_update
                                                            }).text("Invalid")
                                                    ),
                                                    $("<li>").append(
                                                            $("<a>").attr({
                                                                "href": "#",
                                                                "onclick": "setSiteStatus(this, 'waiting'); return false;",
                                                                "data-url": data.urls.admin_status_update
                                                            }).text("Waiting")
                                                    )
                                            )
                                    ),
                                    "&nbsp;&nbsp;",
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
                ],
                "drawCallback": function () {
                    $("[data-toggle=popover]").popover();
                }
            });

            $(".toolbar-bottom-left").append(
                    $("<a>").attr({
                        "href": "#",
                        "onclick": "showAddSiteForm(); return false;"
                    }).addClass("btn btn-default btn-flat").text("Add Site")
            )
        });


        /* TODO delete the following two functions */
        function togglexPathInput(el) {
            var $txt = $(el).closest("tr").find(".txt-site-xpath");
            var $lbl = $(el).closest("tr").find(".lbl-site-xpath");
            if ($lbl.is(":visible")) {
                $lbl.hide();
                $txt.show().focus();
            } else {
                if ($(el).attr("data-url")) {
                    updateXPath($(el).attr("data-url"), {"site_xpath": $txt.val()}, function (response) {
                        $lbl.show().text(response.site.preference.xpath_1);
                        $txt.hide().val(response.site.preference.xpath_1);
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
                    describeServerRespondedError(xhr.status);
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
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function showAddSiteForm() {
            showLoading();
            $.ajax({
                "url": "{{route("admin.site.create")}}",
                "method": "get",
                "success": function (html) {
                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "callback": function (response) {
                                    reloadSiteTable()
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $(this).remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status)
                }
            });
        }

        function showEditCrawlerForm(el) {
            showLoading();
            $.ajax({
                "url": $(el).attr("data-url"),
                "method": "get",
                "success": function(html){
                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "callback": function (response) {
                                    reloadSiteTable()
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $(this).remove();
                    });
                },
                "error": function(xhr, status, error){
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }

        function showEditxPathForm(el) {
            showLoading();
            $.ajax({
                "url": $(el).attr("data-url"),
                "method": "get",
                "success": function(html){
                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "callback": function (response) {
                                    reloadSiteTable()
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $(this).remove();
                    });
                },
                "error": function(xhr, status, error){
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }

        function btnDeleteSiteOnClick(el) {
            confirmP("Delete site", "Do you want to delete this site?", {
                "affirmative": {
                    "text": "Delete",
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
                                    alertP("Delete domain", "The site has been deleted.");
                                    tblSite.row($(el).closest("tr")).remove().draw();
                                } else {
                                    alertP("Error", "Unable to delete site, please try again later.");
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
                    "text": "Cancel",
                    "class": "btn-default btn-flat",
                    "dismiss": true
                }
            })
        }

        function reloadSiteTable() {
            tblSite.ajax.reload(null, false);
        }

        function setSiteStatus(el, status) {
            showLoading();
            $.ajax({
                "url": $(el).attr("data-url"),
                "method": "put",
                "data": {
                    "status": status
                },
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    reloadSiteTable();
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            })
        }
    </script>
@stop