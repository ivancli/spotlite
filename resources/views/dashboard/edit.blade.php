<div class="modal fade" tabindex="-1" role="dialog" id="modal-dashboard-update">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{$dashboard->dashboard_name}}</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::model($dashboard, array('route' => array('dashboard.update', $dashboard->getKey()), 'method'=>'put', "onsubmit"=>"return false", "class" => "form-horizontal sl-form-horizontal", "id"=>"frm-dashboard-update")) !!}
                @include('dashboard.forms.dashboard')
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-update-dashboard">OK</button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#btn-update-dashboard").on("click", function () {
                submitEditDashboard(function (response) {
                    if ($.isFunction(options.callback)) {
                        options.callback(response);
                    }
                });
            });
        }

        function submitEditDashboard(callback) {
            showLoading();
            $.ajax({
                "url": $("#frm-dashboard-update").attr("action"),
                "method": $("#frm-dashboard-update").attr("method"),
                "data": $("#frm-dashboard-update").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        if ($.isFunction(callback)) {
                            callback(response);
                        }
                        $("#modal-dashboard-update").modal("hide");
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-dashboard-update .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Error", "Unable to update dashboard, please try again later.");
                        }
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            })
        }
    </script>
</div>
