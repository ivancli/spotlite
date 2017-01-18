@extends('layouts.adminlte')
@section('title', 'Manage Privacy Policies')

@section('header_title', "Manage Privacy Policies")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>

                    <div class="box-tools pull-right">
                        <a href="{{route('privacy_policy.create')}}" class="btn btn-default btn-sm btn-flat">
                            Create Privacy Policy
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <table id="tbl-privacy-policies" class="table table-bordered table-hover table-striped">
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
        var tblPrivacyPolicies = null;
        $(function () {
            tblPrivacyPolicies = $("#tbl-privacy-policies").DataTable({
                "paging": false,
                "order": [[0, "asc"]],
                "language": {
                    "emptyTable": "No privacy policies in the list",
                    "zeroRecords": "No privacy policies in the list"
                },
                "dom": "<'row'<'col-sm-12'tr>><'row'<'col-sm-5'<\"toolbar-bottom-left\">><'col-sm-7'p>>",
                "columns": [
                    {
                        'name': "privacy_policy_id"
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
                    loadPrivacyPolicies(function (response) {
                        if (typeof response.privacyPolicies != 'undefined') {
                            $.each(response.privacyPolicies, function (index, privacyPolicy) {
                                console.info(privacyPolicy);
                                tblPrivacyPolicies.row.add([
                                    privacyPolicy.privacy_policy_id,
                                    privacyPolicy.content.length / 1000 + " kb",
                                    function () {
                                        return $("<div>").append(
                                                $("<div>").addClass("text-center btn-active").append(
                                                        $("<a>").attr({
                                                            "href": "#",
                                                            "onclick": "toggleActivePrivacyPolicies(this); return false;",
                                                            "data-active": privacyPolicy.active,
                                                            "data-url": privacyPolicy.urls.activeness
                                                        }).append(
                                                                $("<i>").addClass("fa fa-check-circle-o").addClass(function () {
                                                                    if (privacyPolicy.active == 'y') {
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
                                        if (privacyPolicy.created_at != null) {
                                            return timestampToDateTimeByFormat(moment(privacyPolicy.created_at).unix(), datefmt + " " + timefmt)
                                        } else {
                                            return '';
                                        }
                                    },
                                    function () {
                                        if (privacyPolicy.updated_at != null) {
                                            return timestampToDateTimeByFormat(moment(privacyPolicy.updated_at).unix(), datefmt + " " + timefmt)
                                        } else {
                                            return '';
                                        }
                                    },
                                    function () {
                                        return $("<div>").append(
                                                $("<div>").addClass("text-center").append(
                                                        $("<a>").addClass("text-muted").attr({
                                                            "href": privacyPolicy.urls.edit
                                                        }).append(
                                                                $("<i>").addClass("glyphicon glyphicon-pencil")
                                                        ),
                                                        "&nbsp;&nbsp;",
                                                        $("<a>").addClass("text-muted").attr({
                                                            "href": "#",
                                                            "onclick": "deletePrivacyPolicy(this);return false;",
                                                            "data-url": privacyPolicy.urls.delete
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

        function loadPrivacyPolicies(successCallback, errorCallback) {
            $.ajax({
                "url": "{{route('privacy_policy.index')}}",
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

        function toggleActivePrivacyPolicies(el) {
            $.ajax({
                "url": $(el).attr("data-url"),
                "method": "put",
                "data": {
                    "active": $(el).attr("data-active") == "y" ? "n" : "y"
                },
                "dataType": "json",
                "success": function (response) {
                    if (response.status == true) {
                        $(el).attr("data-active", response.privacyPolicy.active);
                        if (response.privacyPolicy.active == 'y') {
                            $("#tbl-privacy-policies").find(".btn-active").find("i").removeClass("text-primary").addClass("text-muted");
                            $(el).find("i").addClass("text-primary").removeClass("text-muted");
                        } else {
                            $(el).find("i").removeClass("text-primary").addClass("text-muted");
                        }
                    } else {
                        alertP("Oops, something went wrong", "Unable to update privacy policy activeness, please try again later.");
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

        function deletePrivacyPolicy(el) {
            confirmP("Delete Privacy Policy", "Are you sure you want to delete this privacy policy?", {
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
                                    alertP("Oops, something went wrong", "Unable to delete privacy policy, please try again later.");
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