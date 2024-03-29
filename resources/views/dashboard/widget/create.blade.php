<div class="modal fade" tabindex="-1" role="dialog" id="modal-dashboard-widget-store">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Chart Characteristics</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::open(array('route' => array('dashboard.widget.store'), 'method'=>'post', "onsubmit"=>"return false", "class" => "form-horizontal sl-form-horizontal", "id"=>"frm-dashboard-widget-store")) !!}
                <input type="hidden" name="dashboard_id" value="{{$dashboard->getKey()}}">
                @include('dashboard.widget.forms.widget')
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-create-dashboard-widget">
                    ADD CHART
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">CANCEL</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#btn-create-dashboard-widget").on("click", function () {
                submitAddContent(function (response) {
                    var params = {
                        "Content Type": $("#sel-dashboard-widget-type-id option:selected").text(),
                        "Chart Type": $("#sel-chart-type").val(),
                        "Timespan": $("#sel-timespan").val(),
                        "Period Resolution": $("#sel-period-resolution").val()
                    };
                    switch ($("#sel-chart-type").val()) {
                        case "site":
                            params["site"] = $("#sel-site option:selected").text();
                        case "product":
                            params["Product"] = $("#sel-product option:selected").text();
                        case "category":
                            params["Category"] = $("#sel-category option:selected").text();
                    }
                    gaDashboardAddContent(params);
                    if ($.isFunction(options.callback)) {
                        options.callback(response);
                    }
                });
            })
        }

        function submitAddContent(callback) {
            showLoading();
            $.ajax({
                "url": $("#frm-dashboard-widget-store").attr("action"),
                "method": $("#frm-dashboard-widget-store").attr("method"),
                "data": $("#frm-dashboard-widget-store").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        if ($.isFunction(callback)) {
                            callback(response);
                        }
                        $("#modal-dashboard-widget-store").modal("hide");
                    } else {
                        alertP("Oops! Something went wrong.", "Unable to add chart, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                        var $errorContainer = $(".errors-container");
                        $errorContainer.empty();
                        $.each(xhr.responseJSON, function (key, error) {
                            $.each(error, function (index, message) {
                                $errorContainer.append(
                                        $("<li>").text(message)
                                );
                            })
                        });
                    } else {
                        describeServerRespondedError(xhr.status);
                    }
                }
            });
        }
    </script>
</div>
