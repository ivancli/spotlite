<div class="modal fade" tabindex="-1" role="dialog" id="modal-group-store">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create A Group</h4>
            </div>
            <div class="modal-body">

                <ul class="text-danger errors-container">
                </ul>
                {!! Form::open(array('route' => array('group.store'), 'method'=>'post', "onsubmit"=>"return false", "id"=>"frm-group-store")) !!}
                <div class="form-group required">
                    {!! Form::label('name', 'Group name', array('class' => 'control-label')) !!}
                    {!! Form::text('name', null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group required">
                    {!! Form::label('url', 'URL', array('class' => 'control-label')) !!}
                    {!! Form::text('url', null, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('description', 'Description', array('class' => 'control-label')) !!}
                    {!! Form::text('description', null, array('class' => 'form-control')) !!}
                </div>
                <input type="hidden" name="active" value="1">
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-create-group">OK</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {
            $("#btn-create-group").on("click", function () {
                submitGroupStore(function (response) {
                    if (response.status == true) {
                        $("#modal-group-store").modal("hide");
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-group-store .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        }
                    }
                }, function (xhr, status, error) {
                    console.info(xhr);
                });
            });
        });

        function submitGroupStore(successCallback, errorCallback) {
            $.ajax({
                "url": $("#frm-group-store").attr("action"),
                "method": "post",
                "data": $("#frm-group-store").serialize(),
                "dataType": "json",
                "success": successCallback,
                "error": errorCallback
            })
        }
    </script>
</div>
