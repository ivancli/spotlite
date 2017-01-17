@extends('layouts.adminlte')
@section('title', 'Manage Terms and Conditions')

@section('header_title', "Manage Terms and Conditions")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
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
                                                $("<div>").addClass("text-center").append(
                                                        $("<a>").attr({
                                                            "href": "#",
                                                            "onclick": "toggleActiveTermsAndConditions(this); return false;"
                                                        }).append(
                                                                $("<i>").addClass("fa fa-check-circle-o").addClass(function () {
                                                                    if (termAndCondition.active == 'y') {
                                                                        return "text-primary";
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
                                                                $("<i>").addClass("fa fa-pencil-square-o")
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
    </script>
@stop