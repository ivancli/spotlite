<div class="modal fade" tabindex="-1" role="dialog" id="modal-site-store">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Site</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::open(array('route' => array('admin.site.store'), 'method'=>'post', "onsubmit"=>"return false", "id"=>"frm-site-store")) !!}
                <div class="form-group required">
                    {!! Form::label('site_url', 'URL', array('class' => 'control-label')) !!}
                    {!! Form::text('site_url', null, array('class' => 'form-control', 'id'=>'txt-site-url', 'placeholder'=>'Enter or copy URL')) !!}
                </div>
                {!! Form::close() !!}

            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-add-site">OK</button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#btn-add-site").on("click", function () {
                submitAddSite(function (response) {
                    if (response.status == true) {
                        if ($.isFunction(options.callback)) {
                            options.callback(response);
                        }
                        $("#modal-site-store").modal("hide");
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-site-store .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Error", "Unable to add site, please try again later.");
                        }
                    }
                })
            });
        }

        function submitAddSite(successCallback, failCallback) {
            showLoading();
            $.ajax({
                "url": $("#frm-site-store").attr("action"),
                "method": $("#frm-site-store").attr("method"),
                "data": $("#frm-site-store").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if ($.isFunction(successCallback)) {
                        successCallback(response);
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    if ($.isFunction(failCallback)) {
                        failCallback(xhr, status, error);
                    }
                }
            })
        }


    </script>
</div>
