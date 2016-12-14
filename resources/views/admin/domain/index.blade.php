@extends('layouts.adminlte')
@section('title', 'Crawler - Domain Management')
@section('header_title', 'Crawler - Domain Management')
@section('breadcrumbs')
{{--    {!! Breadcrumbs::render('admin_domain') !!}--}}
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
                "stateSave": true,
                "pageLength": 25,
                "order": [[0, "asc"]],
                "language": {
                    "emptyTable": "No domains in the list",
                    "zeroRecords": "No domains in the list"
                },
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
                                            $("<span>").text(data.preference.xpath_1).addClass("lbl-domain-xpath"),
                                            $("<input>").attr({
                                                "type": "text",
                                                "value": data.preference.xpath_1
                                            }).hide().addClass("txt-domain-xpath form-control input-sm"),
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "showEditxPathForm(this); return false;",
                                                "data-url": data.urls.xpath_edit
                                            }).append(
                                                    $("<i>").addClass("fa fa-pencil float-right text-muted").css("margin-right", "-20px")
                                            )
                                    )
                            ).html();
                        }
                    },
                    {
                        "name": "crawler_class",
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<div>").css("padding-right", "20px").append(
                                            $("<span>").text(data.crawler_class).addClass("lbl-domain-crawler-class"),
                                            $("<input>").attr({
                                                "type": "text",
                                                "value": data.crawler_class
                                            }).hide().addClass("txt-domain-crawler-class form-control input-sm"),
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "toggleCrawlerClassInput(this); return false;",
                                                "data-url": data.urls.crawler_class_update
                                            }).append(
                                                    $("<i>").addClass("fa fa-pencil float-right text-muted").css("margin-right", "-20px")
                                            )
                                    )
                            ).html();
                        }
                    },
                    {
                        "name": "parser_class",
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<div>").css("padding-right", "20px").append(
                                            $("<span>").text(data.parser_class).addClass("lbl-domain-parser-class"),
                                            $("<input>").attr({
                                                "type": "text",
                                                "value": data.parser_class
                                            }).hide().addClass("txt-domain-parser-class form-control input-sm"),
                                            $("<a>").attr({
                                                "href": "#",
                                                "onclick": "toggleParserClassInput(this); return false;",
                                                "data-url": data.urls.parser_class_update
                                            }).append(
                                                    $("<i>").addClass("fa fa-pencil float-right text-muted").css("margin-right", "-20px")
                                            )
                                    )
                            ).html();
                        }
                    },
                    {
                        "class": "text-center",
                        "sortable": false,
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": "http://" + data.domain_url,
                                        "target": "_blank"
                                    }).append(
                                            $("<i>").addClass("glyphicon glyphicon-globe")
                                    ).addClass("text-muted"),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#",
                                        "data-url": data.urls.delete,
                                        "onclick": "btnDeleteDomainOnClick(this)"
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
                        "onclick": "showAddDomainForm(); return false;"
                    }).addClass("btn btn-default btn-flat").text("Add Domain")
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

        function toggleCrawlerClassInput(el) {
            var $txt = $(el).closest("tr").find(".txt-domain-crawler-class");
            var $lbl = $(el).closest("tr").find(".lbl-domain-crawler-class");
            if ($lbl.is(":visible")) {
                $lbl.hide();
                $txt.show();
            } else {
                /* TODO save xpath */
                updateCrawlerClass($(el).attr("data-url"), {"crawler_class": $txt.val()}, function (response) {
                    $lbl.show().text(response.domain.crawler_class);
                    $txt.hide().val(response.domain.crawler_class);
                }, function (response) {

                });
            }
        }

        function toggleParserClassInput(el) {
            var $txt = $(el).closest("tr").find(".txt-domain-parser-class");
            var $lbl = $(el).closest("tr").find(".lbl-domain-parser-class");
            if ($lbl.is(":visible")) {
                $lbl.hide();
                $txt.show();
            } else {
                /* TODO save xpath */
                updateParserClass($(el).attr("data-url"), {"parser_class": $txt.val()}, function (response) {
                    $lbl.show().text(response.domain.parser_class);
                    $txt.hide().val(response.domain.parser_class);
                }, function (response) {

                });
            }
        }

        function updateCrawlerClass(url, data, successCallback, errorCallback) {
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
                        alertP("Oops! Something went wrong.", "unable to update xpath, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function updateParserClass(url, data, successCallback, errorCallback) {
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
                        alertP("Oops! Something went wrong.", "unable to update xpath, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status)
                }
            })
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
                        alertP("Oops! Something went wrong.", "unable to update xpath, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function showEditxPathForm(el) {
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
                                "callback": function (response) {
                                    tblDomain.ajax.reload();
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

        function btnDeleteDomainOnClick(el) {
            deletePopup("Delete Domain", "Do you want to delete all preferences of this domain?",
                    "By deleting this domain, you will lose the following data:",
                    [
                        "All configuration related to the domain"
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
                                            alertP("Delete domain", "The domain has been deleted.");
                                            $(el).closest(".site-wrapper").remove();
                                            tblDomain.row($(el).closest("tr")).remove().draw();
                                        } else {
                                            alertP("Oops! Something went wrong.", "Unable to delete domain, please try again later.");
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

        function showAddDomainForm() {
            showLoading();
            $.ajax({
                "url": "{{route("admin.domain.create")}}",
                "method": "get",
                "success": function (html) {
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
                    $modal.on("hidden.bs.modal", function () {
                        $(this).remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }
    </script>
@stop