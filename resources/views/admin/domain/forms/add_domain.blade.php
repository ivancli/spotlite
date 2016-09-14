<div class="modal fade" tabindex="-1" role="dialog" id="modal-domain-store">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Domain</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::open(array('route' => array('admin.domain.store'), 'method'=>'post', "onsubmit"=>"return false", "id"=>"frm-domain-store")) !!}
                <div class="form-group required">
                    {!! Form::label('domain_url', 'URL', array('class' => 'control-label', 'placeholder'=>'Enter or copy URL')) !!}
                    {!! Form::text('domain_url', null, array('class' => 'form-control', 'id'=>'txt-domain-url')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('domain_name', 'Domain name', array('class' => 'control-label', 'placeholder'=>'Enter or copy URL')) !!}
                    {!! Form::text('domain_name', null, array('class' => 'form-control', 'id'=>'txt-domain-name')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('domain_xpath', 'xPath', array('class' => 'control-label')) !!}
                    {!! Form::text('domain_xpath', null, array('class' => 'form-control', 'id'=>'txt-domain-xpath')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('crawler_class', 'Crawler class', array('class' => 'control-label')) !!}
                    {!! Form::text('crawler_class', null, array('class' => 'form-control', 'id'=>'txt-domain-crawler')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('parser_class', 'Parser class', array('class' => 'control-label')) !!}
                    {!! Form::text('parser_class', null, array('class' => 'form-control', 'id'=>'txt-domain-parser')) !!}
                </div>
                {!! Form::close() !!}

            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-add-domain">OK</button>
                <button data-dismiss="modal" class="btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {

            $("#btn-add-domain").on("click", function () {
                submitAddDomain(function (response) {
                    if (response.status == true) {
                        if ($.isFunction(options.callback)) {
                            options.callback(response);
                        }
                        $("#modal-domain-store").modal("hide");
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-domain-store .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Error", "Unable to add domain, please try again later.");
                        }
                    }
                })
            });
        }

        function submitAddDomain(successCallback, failCallback) {
            showLoading();
            $.ajax({
                "url": $("#frm-domain-store").attr("action"),
                "method": $("#frm-domain-store").attr("method"),
                "data": $("#frm-domain-store").serialize(),
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
