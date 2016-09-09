<div class="modal fade" tabindex="-1" role="dialog" id="modal-alert-site">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{$product->product_name}} Site Price Alert</h4>
            </div>
            <div class="modal-body">
                <p>{{parse_url($site->site_url)['host']}}</p>
                <ul class="text-danger errors-container">
                </ul>

                {!! Form::model($alert, array('route' => array('alert.site.update', $site->getKey()), 'method'=>'put', "onsubmit"=>"return false", "id"=>"frm-alert-site-update")) !!}
                <input type="hidden" name="alert_owner_id" value="{{$site->getKey()}}">
                <input type="hidden" name="alert_owner_type" value="site">
                <div class="form-group required">
                    {!! Form::label('comparison_price_type', 'Trigger', array('class' => 'control-label')) !!}
                    {!! Form::select('comparison_price_type', array('specific price' => 'Specific Price', 'my price' => 'My Price'), null, array('class' => 'form-control sl-form-control', 'id'=>'sel-price-type')) !!}
                </div>
                <div class="form-group required">
                    {!! Form::label('operator', 'Trend', array('class' => 'control-label')) !!}
                    {!! Form::select('operator', array('<='=>'Equal or Below', '<' => 'Below', '>='=>'Equal or Above', '>'=>'Above'), null, ['class'=>'form-control sl-form-control']) !!}
                </div>
                <div class="form-group required" id="comparison-price-container"
                     style="{{isset($alert) && $alert->comparison_price_type == "my price" ? 'display: none;' : ''}}">
                    {!! Form::label('comparison_price', 'Price Point', array('class' => 'control-label')) !!}
                    {!! Form::text('comparison_price', null, array('class' => 'form-control sl-form-control', 'id' => 'txt-comparison-price')) !!}
                </div>
                <div class="form-group required">
                    {!! Form::label('email[]', 'Notify Emails', array('class' => 'control-label')) !!}
                    {!! Form::select('email[]', $emails, $emails, ['class'=>'form-control', 'multiple' => 'multiple', 'id'=>'sel-email']) !!}
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-update-site-alert">OK</button>
                <button class="btn btn-danger">Delete</button>
                <button data-dismiss="modal" class="btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#sel-site").select2();
            $("#sel-email").select2({
                "tags": true,
                "tokenSeparators": [',', ' ', ';'],
                "placeholder": "Enter Email Address and Press Enter Key"
            });
            $("#sel-price-type").on("change", function () {
                if ($(this).val() == "my price") {
                    $("#comparison-price-container").slideUp();
                } else {
                    $("#comparison-price-container").slideDown();
                }
            });

            $("#btn-update-site-alert").on("click", function () {
                submitUpdateSiteAlert(function (response) {
                    console.info('response', response);
                    if (response.status == true) {
                        alertP("Create/Update Alert", "Alert has been updated.");
                        $("#modal-alert-site").modal("hide");
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-alert-site .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Error", "Unable to create/update alert, please try again later.");
                        }
                    }
                })
            })
        }

        function submitUpdateSiteAlert(callback) {
            if ($("#sel-price-type").val() == "my price") {
                $("#txt-comparison-price").remove();
            }
            $.ajax({
                "url": "{{route('alert.site.update', $site->getKey())}}",
                "method": "put",
                "data": $("#frm-alert-site-update").serialize(),
                "dataType": "json",
                "success": function (response) {
                    if ($.isFunction(callback)) {
                        callback(response);
                    }
                },
                "error": function (xhr, status, error) {

                }
            })
        }
    </script>
</div>
