<div class="modal fade" tabindex="-1" role="dialog" id="modal-site-store">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{$product->product_name}}</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::open(array('route' => array('product_site.store'), 'method'=>'post', "onsubmit"=>"return false", "id"=>"frm-site-store")) !!}
                <input type="hidden" name="product_id" value="{{$product->getKey()}}">
                <div class="form-group required">
                    {!! Form::label('site_url', 'URL', array('class' => 'control-label', 'placeholder'=>'Enter or copy URL')) !!}
                    {!! Form::text('site_url', null, array('class' => 'form-control', 'id'=>'txt-site-url')) !!}
                </div>
                <div class="prices-container" style="display: none;">
                    <p>Please select a correct price from below: </p>
                </div>
                {!! Form::close() !!}

            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-check-price">Check Price</button>
                <button class="btn btn-primary" id="btn-create-site" style="display: none;">OK</button>
                <button class="btn btn-warning" id="btn-report-error" style="display: none;">Error</button>
                <button data-dismiss="modal" class="btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#btn-create-site").on("click", function () {
                showLoading();
                submitSiteStore(function (response) {
                    hideLoading();
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
                }, function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to add site, please try again later.");
                });
            });
            $("#btn-check-price").on("click", function () {
                getPricesCreate();
            });

            $("#btn-report-error").on("click", function () {
                $(".rad-site-id").prop("checked", false);
                showLoading();
                submitSiteStore(function (response) {
                    hideLoading();
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
                }, function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to add site, please try again later.");
                });
            });
        }

        function getPricesCreate() {
            showLoading();
            $.ajax({
                "url": "{{route("site.prices")}}",
                "method": "get",
                "data": {
                    "site_url": $("#txt-site-url").val()
                },
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        if (response.sites.length > 0) {
                            $.each(response.sites, function (index, site) {
                                $(".prices-container").append(
                                        $("<div>").append(
                                                $("<label>").append(
                                                        $("<input>").attr({
                                                            "type": "radio",
                                                            "value": site.site_id,
                                                            "name": "site_id"
                                                        }).addClass("rad-site-id"),
                                                        $("<span>").text('$' + (parseFloat(site.recent_price)).formatMoney(2, '.', ','))
                                                )
                                        ).addClass("radio")
                                )
                            });
                            $(".prices-container").show();
                            $("#btn-report-error").show();
                        } else {
                            $(".prices-container").html("Price will be available soon.");
                        }
                        $(".prices-container").show();
                        $("#btn-check-price").hide();
                        $("#btn-create-site").show();
                    } else {
                        alertP("Error", "Unable to get price, please try again later");
                    }
                },
                "error": function () {
                    hideLoading();
                    alertP("Error", "Unable to get price, please try again later");
                }
            })
        }

        function submitSiteStore(successCallback, errorCallback) {
            $.ajax({
                "url": $("#frm-site-store").attr("action"),
                "method": "post",
                "data": $("#frm-site-store").serialize(),
                "dataType": "json",
                "success": successCallback,
                "error": errorCallback
            })
        }
    </script>
</div>
