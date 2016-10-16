<div class="modal fade" tabindex="-1" role="dialog" id="modal-dashboard-widget-update">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Content</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::model($widget, array('route' => array('dashboard.widget.update', $widget->getKey()), 'method'=>'post', "onsubmit"=>"return false", "class" => "form-horizontal sl-form-horizontal", "id"=>"frm-dashboard-widget-update")) !!}
                @include('dashboard.widget.forms.widget')
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-create-dashboard-widget">
                    Save
                </button>
                <button data-dismiss="modal" class="btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#btn-create-dashboard-widget").on("click", function () {
                submitAddContent(function (response) {
                    if ($.isFunction(options.callback)) {
                        options.callback(response);
                    }
                });
            })
        }

        function submitAddContent(callback) {
            showLoading();
            $.ajax({
                "url": $("#frm-dashboard-widget-update").attr("action"),
                "method": $("#frm-dashboard-widget-update").attr("method"),
                "data": $("#frm-dashboard-widget-update").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        if ($.isFunction(callback)) {
                            callback(response);
                        }
                        $("#modal-dashboard-widget-update").modal("hide");
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-dashboard-widget-update .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Error", "Unable to create dashboard content, please try again later.");
                        }
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                }
            });
        }
    </script>
</div>
