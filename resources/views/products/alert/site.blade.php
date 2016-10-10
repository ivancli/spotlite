<div class="modal fade" tabindex="-1" role="dialog" id="modal-alert-site">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{$site->product->product_name}} Site Price Alert</h4>
            </div>
            <div class="modal-body">
                <p>{{parse_url($site->site_url)['host']}}</p>
                <ul class="text-danger errors-container">
                </ul>

                {!! Form::model($site->alert, array('route' => array('alert.site.update', $site->getKey()), 'method'=>'put', "onsubmit"=>"return false", "id"=>"frm-alert-site-update")) !!}
                <input type="hidden" name="alert_owner_id" value="{{$site->getKey()}}">
                <input type="hidden" name="alert_owner_type" value="site">
                <div class="form-group required">
                    {!! Form::label('comparison_price_type', 'Trigger', array('class' => 'control-label')) !!}
                    {!! Form::select('comparison_price_type', $site->my_price == 'n' ? array('specific price' => 'Specific Price', 'my price' => 'My Price') : array('specific price' => 'Specific Price'), null, array('class' => 'form-control sl-form-control', 'id'=>'sel-price-type')) !!}
                </div>
                <div class="form-group required">
                    {!! Form::label('operator', 'Trend', array('class' => 'control-label')) !!}
                    {!! Form::select('operator', array('=<'=>'Equal or Below', '<' => 'Below', '=>'=>'Equal or Above', '>'=>'Above'), null, ['class'=>'form-control sl-form-control']) !!}
                </div>
                <div class="form-group required" id="comparison-price-container"
                     style="{{isset($site->alert) && $site->alert->comparison_price_type == "my price" ? 'display: none;' : ''}}">
                    {!! Form::label('comparison_price', 'Price Point', array('class' => 'control-label')) !!}
                    {!! Form::text('comparison_price', is_null($site->alert) ? null : number_format($site->alert->comparison_price, 2, '.', ''), array('class' => 'form-control sl-form-control', 'id' => 'txt-comparison-price')) !!}
                </div>
                <div class="form-group required">
                    {!! Form::label('email[]', 'Notify Emails', array('class' => 'control-label')) !!}
{{--                    {!! Form::select('email[]', $emails, $emails, ['class'=>'form-control', 'multiple' => 'multiple', 'id'=>'sel-email']) !!}--}}
                    {!! Form::select('email[]', [auth()->user()->email], [auth()->user()->email], ['class'=>'form-control', 'multiple' => 'multiple', 'id'=>'sel-email', 'disabled' => 'disabled']) !!}
                    <input type="hidden" name="email[]" value="{{auth()->user()->email}}">
                </div>
                <div class="form-group">
                    {!! Form::label('one_off', 'One-off', array('class' => 'control-label')) !!}
                    {!! Form::checkbox('one_off', "y", is_null($site->alert) ? null : $site->alert->one_off == 'y', array('class' => 'sl-form-control')) !!}
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-update-site-alert">OK</button>
                @if(!is_null($site->alert))
                    <button class="btn btn-danger" id="btn-delete-site-alert">Delete</button>
                @endif
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
                    if (response.status == true) {
                        alertP("Create/Update Alert", "Alert has been updated.");
                        $("#modal-alert-site").modal("hide");
                        if ($.isFunction(options.updateCallback)) {
                            options.updateCallback(response);
                        }
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
            });
            $("#btn-delete-site-alert").on("click", function () {

                confirmP("Delete alert", "Do you want to delete this alert?", {
                    "affirmative": {
                        "text": "Delete",
                        "class": "btn-danger",
                        "dismiss": true,
                        "callback": function () {
                            submitDeleteSiteAlert(function (response) {
                                if (response.status == true) {
                                    alertP("Delete Alert", "Alert has been deleted.");
                                    $("#modal-alert-site").modal("hide");
                                    if ($.isFunction(options.deleteCallback)) {
                                        options.deleteCallback(response);
                                    }
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
                                        alertP("Error", "Unable to delete alert, please try again later.");
                                    }
                                }
                            });
                        }
                    },
                    "negative": {
                        "text": "Cancel",
                        "class": "btn-default",
                        "dismiss": true
                    }
                });
            })
        }

        function submitUpdateSiteAlert(callback) {
            showLoading();
            if ($("#sel-price-type").val() == "my price") {
                $("#txt-comparison-price").remove();
            }
            $.ajax({
                "url": "{{route('alert.site.update', $site->getKey())}}",
                "method": "put",
                "data": $("#frm-alert-site-update").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if ($.isFunction(callback)) {
                        callback(response);
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to update site alert, please try again later.");
                }
            })
        }

        function submitDeleteSiteAlert(callback) {
            showLoading();
            $.ajax({
                "url": "{{route('alert.site.destroy', $site->getKey())}}",
                "method": "delete",
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if ($.isFunction(callback)) {
                        callback(response);
                    }
                },
                "error": function () {
                    hideLoading();
                    alertP("Error", "Unable to delete site alert, please try again later.");
                }
            })
        }
    </script>
</div>
