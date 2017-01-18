@extends('layouts.adminlte')
@section('title', 'Manage Terms and Conditions')

@section('header_title', "Manage Terms and Conditions")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>

                    <div class="box-tools pull-right">
                        <a href="{{route('term_and_condition.create')}}" class="btn btn-default btn-sm btn-flat">
                            Create Term and Condition
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <table id="tbl-terms-and-conditions" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th class="shrink">ID</th>
                            <th>Size</th>
                            <th>Is active</th>
                            <th>Created at</th>
                            <th>Updated at</th>
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
@stop

@section('scripts')
    <script type="text/javascript">
        var tblTermsAndConditions = null;
        $(function () {
            tblTermsAndConditions = $("#tbl-terms-and-conditions").DataTable({
                "paging": false,
                "order": [[0, "asc"]],
                "language": {
                    "emptyTable": "No terms and conditions in the list",
                    "zeroRecords": "No terms and conditions in the list"
                },
                "dom": "<'row'<'col-sm-12'tr>><'row'<'col-sm-5'<\"toolbar-bottom-left\">><'col-sm-7'p>>",
                "columns": [
                    {
                        'name': "term_and_condition_id"
                    },
                    {
                        'name': "content"
                    },
                    {
                        'name': "active"
                    },
                    {
                        'name': "created_at"
                    },
                    {
                        'name': "updated_at"
                    },
                    {}
                ],
                "initComplete": function () {
                    loadTermsAndConditions(function (response) {
                        if (typeof response.termsAndConditions != 'undefined') {
                            $.each(response.termsAndConditions, function (index, termAndCondition) {
                                console.info(termAndCondition);
                                tblTermsAndConditions.row.add([
                                    termAndCondition.term_and_condition_id,
                                    termAndCondition.content.length / 1000 + " kb",
                                    function () {
                                        return $("<div>").append(
                                                $("<div>").addClass("text-center btn-active").append(
                                                        $("<a>").attr({
                                                            "href": "#",
                                                            "onclick": "toggleActiveTermsAndConditions(this); return false;",
                                                            "data-active": termAndCondition.active,
                                                            "data-url": termAndCondition.urls.activeness
                                                        }).append(
                                                                $("<i>").addClass("fa fa-check-circle-o").addClass(function () {
                                                                    if (termAndCondition.active == 'y') {
                                                                        return "text-primary ";
                                                                    } else {
                                                                        return "text-muted";
                                                                    }
                                                                })
                                                        )
                                                )
                                        ).html();
                                    },
                                    function () {
                                        if (termAndCondition.created_at != null) {
                                            return timestampToDateTimeByFormat(moment(termAndCondition.created_at).unix(), datefmt + " " + timefmt)
                                        } else {
                                            return '';
                                        }
                                    },
                                    function () {
                                        if (termAndCondition.updated_at != null) {
                                            return timestampToDateTimeByFormat(moment(termAndCondition.updated_at).unix(), datefmt + " " + timefmt)
                                        } else {
                                            return '';
                                        }
                                    },
                                    function () {
                                        return $("<div>").append(
                                                $("<div>").addClass("text-center").append(
                                                        $("<a>").addClass("text-muted").attr({
                                                            "href": termAndCondition.urls.edit
                                                        }).append(
                                                                $("<i>").addClass("glyphicon glyphicon-pencil")
                                                        ),
                                                        "&nbsp;&nbsp;",
                                                        $("<a>").addClass("text-muted").attr({
                                                            "href": "#",
                                                            "onclick": "deleteTermAndCondition(this);return false;",
                                                            "data-url": termAndCondition.urls.delete
                                                        }).append(
                                                                $("<i>").addClass("glyphicon glyphicon-trash")
                                                        )
                                                )
                                        ).html();
                                    }
                                ]).draw(false);
                            });
                        }
                    })
                }
            });
        });

        function loadTermsAndConditions(successCallback, errorCallback) {
            $.ajax({
                "url": "{{route('term_and_condition.index')}}",
                "dataType": "json",
                "success": function (response) {
                    if (response.status == true) {
                        if ($.isFunction(successCallback)) {
                            successCallback(response);
                        }
                    } else {
                        if ($.isFunction(errorCallback)) {
                            errorCallback(response);
                        }
                    }
                },
                "error": function (xhr, status, error) {
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function toggleActiveTermsAndConditions(el) {
            $.ajax({
                "url": $(el).attr("data-url"),
                "method": "put",
                "data": {
                    "active": $(el).attr("data-active") == "y" ? "n" : "y"
                },
                "dataType": "json",
                "success": function (response) {
                    if (response.status == true) {
                        $(el).attr("data-active", response.termAndCondition.active);
                        if (response.termAndCondition.active == 'y') {
                            $("#tbl-terms-and-conditions").find(".btn-active").find("i").removeClass("text-primary").addClass("text-muted");
                            $(el).find("i").addClass("text-primary").removeClass("text-muted");
                        } else {
                            $(el).find("i").removeClass("text-primary").addClass("text-muted");
                        }
                    } else {
                        alertP("Oops, something went wrong", "Unable to update term and condition activeness, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                        var errorMsg = "";
                        $.each(xhr.responseJSON, function (key, error) {
                            $.each(error, function (index, message) {
                                errorMsg += message + " ";
                            })
                        });
                        alertP("Oops! Something went wrong.", errorMsg);
                    } else {
                        describeServerRespondedError(xhr.status);
                    }
                }
            })
        }

        function deleteTermAndCondition(el) {
            confirmP("Delete Term And Condition", "Are you sure you want to delete this term and condition?", {
                "affirmative": {
                    "class": "btn-danger btn-flat",
                    "text": "DELETE",
                    "callback": function () {
                        showLoading();
                        $.ajax({
                            "url": $(el).attr("data-url"),
                            "method": "delete",
                            "dataType": "json",
                            "success": function (response) {
                                hideLoading();
                                if (response.status == true) {
                                    $(el).closest("tr").remove();
                                } else {
                                    alertP("Oops, something went wrong", "Unable to delete term and condition, please try again later.");
                                }
                            },
                            "error": function (xhr, status, error) {
                                hideLoading();
                                if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                                    var errorMsg = "";
                                    $.each(xhr.responseJSON, function (key, error) {
                                        $.each(error, function (index, message) {
                                            errorMsg += message + " ";
                                        })
                                    });
                                    alertP("Oops! Something went wrong.", errorMsg);
                                } else {
                                    describeServerRespondedError(xhr.status);
                                }
                            }
                        })
                    },
                    "dismiss": true
                },
                "negative": {
                    "class": "btn-default btn-flat",
                    "text": "CANCEL",
                    "dismiss": true
                }
            });
        }
    </script>
@stop