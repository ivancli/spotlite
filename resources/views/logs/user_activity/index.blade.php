@extends('layouts.adminlte')
@section('title', 'User Activity Logs')
@section('header_title', isset($user) ? "{$user->first_name} {$user->last_name} - Activity Logs" : 'User Activity Logs')
@section('breadcrumbs')
    {!! Breadcrumbs::render('user_activity_log') !!}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table id="tbl-log" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th class="shrink">ID</th>
                            <th>User</th>
                            <th>Activity</th>
                            <th>Date time</th>
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
        var tblLog = null;
        $(function () {
            jQuery.fn.dataTable.Api.register('processing()', function (show) {
                return this.iterator('table', function (ctx) {
                    ctx.oApi._fnProcessingDisplay(ctx, show);
                });
            });
            tblLog = $("#tbl-log").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "pageLength": 25,
                "order": [[3, "desc"]],
                "language": {
                    "emptyTable": "No logs in the list"
                },
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'><'col-sm-7'p>>",
                "ajax": {
                    "url": "{{route(request()->route()->getName(), isset($user) ? $user->getKey() : null)}}",
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
                        "name": "user_activity_log_id",
                        "data": "user_activity_log_id"
                    },
                    {
                        "class": "text-center",
                        "data": function (data) {
                            return $("<div>").append(
                                    $("<a>").attr({
                                        "href": data.urls.owner
                                    }).addClass("text-muted").text(data.owner.first_name + " " + data.owner.last_name)
                            ).html();
                        }
                    },
                    {
                        "name": "activity",
                        "data": "activity"
                    },
                    {
                        "name": "created_at",
                        "data": "created_at"
                    }
                ]
            });
        });
    </script>
@stop