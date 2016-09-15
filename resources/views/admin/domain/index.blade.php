@extends('layouts.adminlte')
@section('title', 'Crawler - Domain Management')
@section('header_title', 'Crawler - Domain Management')
@section('breadcrumbs')
    {!! Breadcrumbs::render('admin_domain') !!}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table id="tbl-domain" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th class="shrink">ID</th>
                            <th>Domain URL</th>
                            <th>Domain Name</th>
                            <th>Domain xPath</th>
                            <th>Crawler Class</th>
                            <th>Parser Class</th>
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
        var tblDomain = null;

        $(function () {
            jQuery.fn.dataTable.Api.register('processing()', function (show) {
                return this.iterator('table', function (ctx) {
                    ctx.oApi._fnProcessingDisplay(ctx, show);
                });
            });
            tblDomain = $("#tbl-domain").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "pageLength": 25,
                "order": [[0, "asc"]],
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'<\"toolbar-bottom-left\">><'col-sm-7'p>>",
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
                        "name": "domain_id",
                        "data": "domain_id"
                    },
                    {
                        "name": "domain_url",
                        "data": "domain_url" //you might wanna change this to be an anchor
                    },
                    {
                        "name": "domain_name",
                        "data": "domain_name"
                    },
                    {
                        "name": "domain_xpath",
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<div>").css("padding-right", "20px").append(
                                            $("<span>").text(data.domain_xpath).addClass("lbl-domain-xpath"),
                                            $("<input>").attr({
                                                "type": "text",
                                                "value": data.domain_xpath
                                            }).hide().addClass("txt-domain-xpath form-control input-sm"),
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "togglexPathInput(this); return false;",
                                                "data-url": data.urls.update
                                            }).append(
                                                    $("<i>").addClass("fa fa-pencil float-right text-muted").css("margin-right", "-20px")
                                            )
                                    )
                            ).html();
                        }
                    },
                    {
                        "name": "crawler_class",
                        "data": "crawler_class"
                    },
                    {
                        "name": "parser_class",
                        "data": "parser_class"
                    },
                    {
                        "class": "text-center",
                        "sortable": false,
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": data.domain_url,
                                        "target": "_blank"
                                    }).append(
                                            $("<i>").addClass("fa fa-globe")
                                    ).addClass("text-muted"),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#",
                                        "data-url": data.urls.delete,
                                        "onclick": "btnDeleteSiteOnClick(this)"
                                    }).append(
                                            $("<i>").addClass("fa fa-trash-o")
                                    ).addClass("text-muted text-danger")
                            ).html()
                        }
                    }
                ]
            });
            $(".toolbar-bottom-left").append(
                    $("<a>").attr({
                        "href": "#",
                        "onclick": "showAddDomainForm(); return false;"
                    }).addClass("btn btn-default").text("Add Domain")
            )
        });

        function togglexPathInput(el) {
            var $txt = $(el).closest("tr").find(".txt-domain-xpath");
            var $lbl = $(el).closest("tr").find(".lbl-domain-xpath");
            if ($lbl.is(":visible")) {
                $lbl.hide();
                $txt.show();
            } else {
                /* TODO save xpath */
                updateXPath($(el).attr("data-url"), {"domain_xpath": $txt.val()}, function (response) {
                    $lbl.show().text(response.domain.domain_xpath);
                    $txt.hide().val(response.domain.domain_xpath);
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

        function btnDeleteSiteOnClick(el) {
            confirmP("Delete domain", "Do you want to delete all preferences of this domain?", {
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

        function showAddDomainForm()
        {
            showLoading();
            $.get("{{route("admin.domain.create")}}", function (html) {
                hideLoading();
                var $modal = $(html);
                $modal.modal();
                $modal.on("shown.bs.modal", function () {
                    if ($.isFunction(modalReady)) {
                        modalReady({
                            "callback": function (response) {
                                tblDomain.ajax.reload();
                            }
                        })
                    }
                });
                $modal.on("hidden.bs.modal", function(){
                    $(this).remove();
                });
            })
        }
    </script>
@stop