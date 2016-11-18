@extends('layouts.adminlte')
@section('title', 'Group List')
@section('header_title', 'Group List')
@section('breadcrumbs')
    {!! Breadcrumbs::render('group') !!}
@stop
@section('content')
    @include('um.partials.banner_stats')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Group List</h3>

                    <div class="box-tools pull-right">
                        <a href="{{route('um.group.create')}}" class="btn btn-default btn-sm btn-flat">Create Group</a>
                    </div>
                </div>
                <div class="box-body">
                    <table id="tbl-groups" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Active</th>
                            <th>URL</th>
                            <th>Description</th>
                            <th>Created at</th>
                            <th>Updated at</th>
                            <th width="10%"></th>
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
        var tblGroups;
        $(function () {
            jQuery.fn.dataTable.Api.register('processing()', function (show) {
                return this.iterator('table', function (ctx) {
                    ctx.oApi._fnProcessingDisplay(ctx, show);
                });
            });
            tblGroups = $("#tbl-groups").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "language": {
                    "emptyTable": "No groups in the list",
                    "zeroRecords": "No groups in the list"
                },
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'><'col-sm-7'p>>",
                "ajax": "{{route('um.group.index')}}",
                "columns": [
                    {
                        "name": "group_id",
                        "data": "group_id"
                    },
                    {
                        "name": "name",
                        "data": "name"
                    },
                    {
                        "name": "active",
                        "data": "active"
                    },
                    {
                        "name": "url",
                        "data": "url"
                    },
                    {
                        "name": "description",
                        "data": "description"
                    },
                    {
                        "name": "created_at",
                        "data": function(data){
                            return timestampToDateTimeByFormat(moment(data.created_at).unix(), datefmt + " " + timefmt)
                        }
                    },
                    {
                        "name": "updated_at",
                        "data": function(data){
                            return timestampToDateTimeByFormat(moment(data.updated_at).unix(), datefmt + " " + timefmt)
                        }
                    },
                    {
                        "class": "text-center",
                        "sortable": false,
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": data.urls.show
                                    }).addClass("text-muted").append(
                                            $("<i>").addClass("glyphicon glyphicon-search")
                                    ),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": data.urls.edit
                                    }).addClass("text-muted").append(
                                            $("<i>").addClass("glyphicon glyphicon-pencil")
                                    ),
                                    "&nbsp;",
                                    $("<a>").attr({
                                        "href": "#",
                                        "onclick": "deleteGroup('" + data.urls.delete + "')"
                                    }).addClass('text-danger').append(
                                            $("<i>").addClass("glyphicon glyphicon-trash")
                                    )
                            ).html();
                        }
                    }
                ]
            });
        });

        function deleteGroup(url, callback) {
            if (confirm("Do you want to delete this group?")) {
                tblGroups.processing(true);
                $.ajax({
                    "url": url,
                    "type": "delete",
                    "data": {
                        "_token": "{!! csrf_token() !!}"
                    },
                    'cache': false,
                    'dataType': "json",
                    "success": function (response) {
                        tblGroups.processing(false);
                        if ($.isFunction(callback)) {
                            callback(response);
                        }
                        tblGroups.ajax.reload(null, false);
                    },
                    "error": function (xhr, status, error) {
                        alert("Unable to delete group, please try again later.");
                        describeServerRespondedError(xhr.status);
                    }
                })
            }
        }
    </script>
@stop