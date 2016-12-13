<div class="modal fade" tabindex="-1" role="dialog" id="modal-dashboard-store">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Create a New Dashboard</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::open(array('route' => array('dashboard.store'), 'method'=>'post', "onsubmit"=>"return false", "id"=>"frm-dashboard-store")) !!}
                @include('dashboard.forms.dashboard')
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-create-dashboard">CONFIRM</button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">CANCEL</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#btn-create-dashboard").on("click", function () {
                submitCreateDashboard(function (response) {
                    if ($.isFunction(options.callback)) {
                        options.callback(response);
                    }
                });
            });
        }

        function submitCreateDashboard(callback) {
            showLoading();
            $.ajax({
                "url": $("#frm-dashboard-store").attr("action"),
                "method": $("#frm-dashboard-store").attr("method"),
                "data": $("#frm-dashboard-store").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        if ($.isFunction(callback)) {
                            callback(response);
                        }
                        $("#modal-dashboard-store").modal("hide");
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-dashboard-store .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Error", "Unable to create dashboard, please try again later.");
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
