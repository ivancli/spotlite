@extends('layouts.adminlte')
@section('title', 'Permission List')
@section('content')
    @include('um.partials.banner_stats')
    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Permission List</h3>

                    <div class="box-tools pull-right">
                        <a href="{{route('um.permission.create')}}" class="btn btn-default btn-sm">Create Permission</a>
                    </div>
                </div>
                <div class="box-body">
                    <table id="tbl-permissions" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Display name</th>
                            <th>Description</th>
                            <th>Parent permission</th>
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
        var tblPermissions;
        $(function () {
            jQuery.fn.dataTable.Api.register('processing()', function (show) {
                return this.iterator('table', function (ctx) {
                    ctx.oApi._fnProcessingDisplay(ctx, show);
                });
            });
            tblPermissions = $("#tbl-permissions").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'><'col-sm-7'p>>",
                "ajax": "{{route('um.permission.index')}}",
                "columns": [
                    {
                        "name": "permission_id",
                        "data": "permission_id"
                    },
                    {
                        "name": "name",
                        "data": "name"
                    },
                    {
                        "name": "display_name",
                        "data": "display_name"
                    },
                    {
                        "name": "description",
                        "data": "description"
                    },
                    {
                        "name": "parent_perm.name",
                        "data": function (data) {
                            if (data.parent_perm != null) {
                                return $("<div>").append(
                                        $("<a>").text(data.parent_perm.name).attr({
                                            "href": data.parent_perm.urls.show
                                        })
                                ).html();
                            } else {
                                return null;
                            }
                        }
                    },
                    {
                        "name": "created_at",
                        "data": "created_at"
                    },
                    {
                        "name": "updated_at",
                        "data": "updated_at"
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
                                        "onclick": "deletePermission('" + data.urls.delete + "')"
                                    }).addClass('text-danger').append(
                                            $("<i>").addClass("glyphicon glyphicon-trash")
                                    )
                            ).html();
                        }
                    }
                ]
            });
        });

        function deletePermission(url, callback) {
            if (confirm("Do you want to delete this permission?")) {
                tblPermissions.processing(true);
                $.ajax({
                    "url": url,
                    "type": "delete",
                    "data": {
                        "_token": "{!! csrf_token() !!}"
                    },
                    'cache': false,
                    'dataType': "json",
                    "success": function (response) {
                        tblPermissions.processing(false);
                        if ($.isFunction(callback)) {
                            callback(response);
                        }
                        tblPermissions.ajax.reload(null, false);
                    },
                    "error": function () {
                        alert("Unable to delete permission, please try again later.");
                    }
                })
            }
        }
    </script>
@stop