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
                "data": {
                    "dashboard_id": "{{$dashboard->getKey()}}"
                },
                "success": function (html) {
                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "callback": function (response) {
//                                    window.location.reload();
                                    if (response.status == true && typeof response.dashboardWidget != 'undefined') {
                                        getWidget(response.dashboardWidget.urls['show']);
                                    }

                                    console.info('response', response);
                                    /* TODO append new widget */
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

        function getWidget(url) {
            showLoading();
            $.ajax({
                "url": url,
                "method": "get",
                "success": function (html) {
                    hideLoading();
                    var $newWidget = $("<div>").addClass("col-md-3 widget-container").css("display", "none").html(html);
                    $(".widgets-container").append(
                            $newWidget
                    );
                    $newWidget.slideDown();
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to load dashboard content, please try again later.");
                }
            });
        }

        function deleteWidget(el) {
            confirmP("Delete content", "Do you want to delete this content?", {
                "affirmative": {
                    "text": "Delete",
                    "class": "btn-danger",
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
                                    $(el).closest(".widget-container").slideUp(function () {
                                        $(this).remove();
                                    })
                                } else {
                                    alertP("Error", "Unable to delete content, please try again later.");
                                }
                            },
                            "error": function (xhr, status, error) {
                                hideLoading();
                                alertP("Error", "Unable to delete content, please try again later.");
                            }
                        })
                    }
                },
                "negative": {
                    "text": "Cancel",
                    "class": "btn-default",
                    "dismiss": true
                }
            })
        }
    </script>
@stop