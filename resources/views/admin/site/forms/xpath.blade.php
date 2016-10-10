<div class="modal fade" tabindex="-1" role="dialog" id="modal-xpath-update">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Update xPath</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::model($site->preference, array('route' => array('admin.site.xpath.update', $site->getKey()), 'method'=>'put', "onsubmit"=>"return false", "id"=>"frm-xpath-update")) !!}
                <div class="form-group">
                    {!! Form::label('xpath_1', 'xPath 1', array('class' => 'control-label')) !!}
                    {!! Form::text('xpath_1', null, array('class' => 'form-control', 'id'=>'txt-site-url', 'placeholder'=>'Enter the primary xPath')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('xpath_2', 'xPath 2', array('class' => 'control-label')) !!}
                    {!! Form::text('xpath_2', null, array('class' => 'form-control', 'id'=>'txt-site-url', 'placeholder'=>'Enter the secondary xPath')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('xpath_3', 'xPath 3', array('class' => 'control-label')) !!}
                    {!! Form::text('xpath_3', null, array('class' => 'form-control', 'id'=>'txt-site-url', 'placeholder'=>'Enter the optional xPath')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('xpath_4', 'xPath 4', array('class' => 'control-label')) !!}
                    {!! Form::text('xpath_4', null, array('class' => 'form-control', 'id'=>'txt-site-url', 'placeholder'=>'Enter the optional xPath')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('xpath_5', 'xPath 5', array('class' => 'control-label')) !!}
                    {!! Form::text('xpath_5', null, array('class' => 'form-control', 'id'=>'txt-site-url', 'placeholder'=>'Enter the optional xPath')) !!}
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-update-xpath">OK</button>
                <button data-dismiss="modal" class="btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#btn-update-xpath").on("click", function () {
                submitUpdateXpath(function (response) {
                    if (response.status == true) {
                        if ($.isFunction(options.callback)) {
                            options.callback();
                            $("#modal-xpath-update").modal("hide");
                        }
                    } else {
                        alertP('Update xPath', 'Unable to update xPath, please try again later.');
                    }
                }, function (xhr, status, error) {
                    alertP('Update xPath', 'Unable to update xPath, please try again later.');
                })
            })
        }

        function submitUpdateXpath(successCallback, failCallback) {
            showLoading();
            $.ajax({
                "url": $("#frm-xpath-update").attr("action"),
                "method": $("#frm-xpath-update").attr("method"),
                "data": $("#frm-xpath-update").serialize(),
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
