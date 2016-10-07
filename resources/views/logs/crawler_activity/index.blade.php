@extends('layouts.adminlte')
@section('title', 'Crawler Activity Logs')
@section('header_title', "Crawler Activity Logs")
@section('breadcrumbs')
    {!! Breadcrumbs::render('crawler_activity_log') !!}
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
                            <th>Status</th>
                            <th>Message</th>
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
                    "emptyTable": "No logs in the list",
                    "zeroRecords": "No logs in the list"
                },
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'><'col-sm-7'p>>",
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
                        "name": "crawler_activity_log_id",
                        "data": "crawler_activity_log_id"
                    },
                    {
                        "class": "text-center",
                        "name": "status",
                        "data": "status"
                    },
                    {
                        "name": "message",
                        "data": function (data) {

                            var message = JSON.parse(data.message);
                            console.info('message', message);

                            return $("<div>").append(
                                    $("<div>").text('crawler_id: ' + message.crawler_id),
                                    $("<div>").append(
                                            $("<a>").attr({
                                                "href": message.url
                                            }).text(getDomainFromURL(message.url))
                                    ),
                                    $("<div>").text("xpath: " + message.xpath)
                            ).html();
                        }
                    },
                    {
                        "name": "created_at",
                        "data": function(data){
                            return timestampToDateTimeByFormat(moment(data.created_at).unix(), datefmt + " " + timefmt)
                        }
                    }
                ]
            });
        });
    </script>
@stop