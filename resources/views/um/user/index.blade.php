@extends('layouts.adminlte')
@section('title', 'User List')
@section('header_title', 'Users')
@section('breadcrumbs')
    {!! Breadcrumbs::render('user') !!}
@stop
@section('content')
    @include('um.partials.banner_stats')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">User List</h3>

                    <div class="box-tools pull-right">
                        <a href="{{route('um.user.create')}}" class="btn btn-default btn-sm btn-flat">Create User</a>
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-hover table-striped" id="tbl-users">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Email</th>
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
        var tblUsers = null;
        $(function () {
            jQuery.fn.dataTable.Api.register('processing()', function (show) {
                return this.iterator('table', function (ctx) {
                    ctx.oApi._fnProcessingDisplay(ctx, show);
                });
            });
            tblUsers = $("#tbl-users").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "language": {
                    "emptyTable": "No users in the list",
                    "zeroRecords": "No users in the list"
                },
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'><'col-sm-7'p>>",
                "ajax": "{{route('um.user.index')}}",
                "columns": [
                    {
                        "name": "user_id",
                        "data": "user_id"
                    },
                    {
                        "name": "first_name",
                        "data": "first_name"
                    },
                    {
                        "name": "last_name",
                        "data": "last_name"
                    },
                    {
                        "name": "email",
                        "data": "email"
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
                                        "onclick": "deleteUser('" + data.urls.delete + "')"
                                    }).addClass('text-danger').append(
                                            $("<i>").addClass("glyphicon glyphicon-trash")
                                    )
                            ).html();
//                            return
                        }
                    }
                ]
            });
        });

        function deleteUser(url, callback) {
            if (confirm("Do you want to delete this user?")) {
                tblUsers.processing(true);
                $.ajax({
                    "url": url,
                    "type": "delete",
                    "data": {
                        "_token": "{!! csrf_token() !!}"
                    },
                    'cache': false,
                    'dataType': "json",
                    "success": function (response) {
                        tblUsers.processing(false);
                        if ($.isFunction(callback)) {
                            callback(response);
                        }
                        tblUsers.ajax.reload(null, false);
                    },
                    "error": function (xhr, status, error) {
                        describeServerRespondedError(xhr.status);
                    }
                })
            }
        }
    </script>
@stop