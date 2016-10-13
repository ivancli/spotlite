@extends('layouts.adminlte')
@section('title', 'Dashboard')
@section('content')
    @if(isset($dashboard))
        @include('dashboard.templates.' . $dashboard->template->dashboard_template_name)
    @endif
@stop

@section('scripts')
    <script type="text/javascript">
        function applyFilters() {

        }

        function addWidget() {
            showLoading();
            $.ajax({
                "url": "{{route("dashboard.widget.create")}}",
                "method": "get",
                "success": function (html) {
                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "callback": function (response) {
                                    window.location.reload();
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $("#modal-dashboard-widget-store").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    alertP("Error", "Unable to add content, please try again later.");
                }
            })
        }
    </script>
@stop