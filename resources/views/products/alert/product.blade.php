<div class="modal fade" tabindex="-1" role="dialog" id="modal-alert-product">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{$product->product_name}} Product Price Alert</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>

                {!! Form::model($product->alert, array('route' => array('alert.product.update', $product->getKey()), 'method'=>'put', "onsubmit"=>"return false", "id"=>"frm-alert-product-update", "class" => "nl-form")) !!}
                <input type="hidden" name="alert_owner_id" value="{{$product->getKey()}}">
                <input type="hidden" name="alert_owner_type" value="product">


                <p>
                    Send me a alert when a price goes
                    &nbsp; {!! Form::select('operator', array('=<'=>'equal or below', '<' => 'below', '=>'=>'equal or above', '>'=>'above'), null) !!}
                    &nbsp; {!! Form::select('comparison_price_type', !is_null($product->myPriceSite()) ? array('specific price' => 'specific price', 'my price' => 'my price') : array('specific price' => 'specific price'), null, array('id'=>'sel-price-type')) !!}
                    <span class="specific-price-sentence">
                        (
                        $ {!! Form::text('comparison_price', is_null($product->alert) ? null : number_format($product->alert->comparison_price, 2, '.', ''), array('placeholder' => 'enter a price' , 'id' => 'txt-comparison-price')) !!}
                        )
                    </span>
                    <br>
                    excluding &nbsp;
                    <span style="line-height: normal;">
                        {!! Form::select('site_id[]', $sites, $excludedSites, array('id'=>'sel-site', 'placeholder' => 'select a site')) !!}
                        .
                        </span>
                    <p>
                        This alert should be sent
                        &nbsp;
                        {!! Form::select('one_off', array('n'=>'every time', 'y' => 'just once'), is_null($product->alert) ? null : $product->alert->one_off) !!}
                    </p>
                </p>

                <div class="form-group required">
                    {!! Form::label('email[]', 'Email Address', array('class' => 'control-label')) !!}
                    {!! Form::select('email[]', [auth()->user()->email], [auth()->user()->email], ['class'=>'form-control', 'multiple' => 'multiple', 'id'=>'sel-email', 'disabled' => 'disabled']) !!}
                    <input type="hidden" name="email[]" value="{{auth()->user()->email}}">
                </div>
                <div class="nl-overlay"></div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-update-product-alert">OK</button>
                @if(!is_null($product->alert))
                    <button class="btn btn-danger btn-flat" id="btn-delete-product-alert">Delete</button>
                @endif
                <button data-dismiss="modal" class="btn btn-default btn-flat">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            updateSentenceVisibility();
            $("#sel-email").select2({
                "tags": true,
                "tokenSeparators": [',', ' ', ';'],
                "placeholder": "Enter Email Address and Press Enter Key"
            });
            $("#sel-price-type").on("change", function () {
                updateSentenceVisibility();
            });
            var nlform = new NLForm($("#frm-alert-product-update").get(0));

            $("#btn-update-product-alert").on("click", function () {
                submitUpdateProductAlert(function (response) {
                    if (response.status == true) {
                        var gaParams = {
                            "Trigger": $("#comparison_price_type option:selected").text(),
                            "Trend": $("#operator option:selected").text(),
                            "One-off": $("#one_off").is(":checked") ? "yes" : "no"
                        };
                        if ($("#comparison_price_type").val() == "specific price") {
                            gaParams["Price Point"] = $("#txt-comparison-price").val();
                        }
                        gaAddProductAlert(gaParams);

                        alertP("Create/Update Alert", "Alert has been updated.");
                        $("#modal-alert-product").modal("hide");
                        if ($.isFunction(options.updateCallback)) {
                            options.updateCallback(response);
                        }
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-alert-product .errors-container");
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
            $("#btn-delete-product-alert").on("click", function () {

                confirmP("Delete alert", "Are you sure you want to delete the {{$product->product_name}} Product Alert?", {
                    "affirmative": {
                        "text": "Delete",
                        "class": "btn-danger btn-flat",
                        "dismiss": true,
                        "callback": function () {
                            submitDeleteProductAlert(function (response) {
                                if (response.status == true) {
                                    alertP("Delete Alert", "Alert has been deleted.");
                                    $("#modal-alert-product").modal("hide");
                                    if ($.isFunction(options.deleteCallback)) {
                                        options.deleteCallback(response);
                                    }
                                } else {
                                    if (typeof response.errors != 'undefined') {
                                        var $errorContainer = $("#modal-alert-product .errors-container");
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
                        "class": "btn-default btn-flat",
                        "dismiss": true
                    }
                });
            })
        }

        function submitUpdateProductAlert(callback) {
            showLoading();
            if ($("#sel-price-type").val() == "my price") {
                $("#txt-comparison-price").remove();
            }
            $.ajax({
                "url": "{{route('alert.product.update', $product->getKey())}}",
                "method": "put",
                "data": $("#frm-alert-product-update").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if ($.isFunction(callback)) {
                        callback(response);
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function submitDeleteProductAlert(callback) {
            showLoading();
            $.ajax({
                "url": "{{route('alert.product.destroy', $product->getKey())}}",
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
                    describeServerRespondedError(xhr.status);
                }
            })
        }


        function updateSentenceVisibility() {
            var $specificPriceSentence = $(".specific-price-sentence").show();
            switch ($("#sel-price-type").val()) {
                case "specific price":
                    $specificPriceSentence.show();
                    break;
                case "my price":
                    $specificPriceSentence.hide();
                    break;
            }
        }
    </script>
</div>
