<div class="modal fade" tabindex="-1" role="dialog" id="modal-site-update">
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
                {!! Form::model($site, array('route' => array('site.update', $site->getKey()), 'method'=>'put', "onsubmit"=>"return false", "id"=>"frm-site-update")) !!}
                <input type="hidden" name="product_id" value="{{$product->getKey()}}">
                <input type="hidden" name="product_site_id" value="{{$product_site_id}}">
                <div class="form-group required">
                    {!! Form::label('site_url', 'URL', array('class' => 'control-label', 'placeholder'=>'Enter or copy URL')) !!}
                    {!! Form::text('site_url', null, array('class' => 'form-control', 'id'=>'txt-site-url', 'onkeyup'=>'updateEditSiteModelButtonStatus(this)')) !!}
                </div>

                @if(!is_null($site->recent_price))
                    <p>Current price: ${{number_format($site->recent_price, 2, '.', ',')}}</p>
                @endif
                <div class="prices-container">
                    @if(isset($sites) && $sites->count() > 0)
                        <p>Please select a correct price from below: </p>
                        @foreach($sites as $priceSite)
                            <div class="radio">
                                <label>
                                    <input type="radio" name="site_id" class="rad-site-id"
                                           value="{{$priceSite->getKey()}}" {{$priceSite->getKey() == $site->getKey() ? 'checked="checked"' : ""}}>
                                    ${{number_format($priceSite->recent_price, 2, '.', ',')}}
                                </label>
                            </div>
                        @endforeach
                    @else
                        <p>Price will be available soon.</p>
                    @endif
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-check-price" style="display: none;">Check Price</button>
                <button class="btn btn-primary" id="btn-edit-site">OK</button>

                <button class="btn btn-warning" id="btn-report-error"
                        style="{{!isset($sites) || $sites->count() == 0 ? 'display: none;' : ""}}">Error
                </button>
                <button data-dismiss="modal" class="btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#btn-edit-site").on("click", function () {
                showLoading();
                submitSiteUpdate(function (response) {
                    hideLoading();
                    if (response.status == true) {
                        if ($.isFunction(options.callback)) {
                            options.callback(response);
                        }
                        $("#modal-site-update").modal("hide");
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-site-update .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Error", "Unable to update this site, please try again later.");
                        }
                    }
                }, function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to update this site, please try again later.");
                });
            });
            $("#btn-check-price").on("click", function () {
                getPricesEdit();
            });

            $("#btn-report-error").on("click", function () {
                $(".rad-site-id").prop("checked", false);
                showLoading();
                submitSiteUpdate(function (response) {
                    hideLoading();
                    if (response.status == true) {
                        if ($.isFunction(options.callback)) {
                            options.callback(response);
                        }
                        $("#modal-site-update").modal("hide");
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-site-update .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Error", "Unable to update this site, please try again later.");
                        }
                    }
                }, function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to update this site, please try again later.");
                });
            });
        }

        function submitSiteUpdate(successCallback, errorCallback) {
            $.ajax({
                "url": $("#frm-site-update").attr("action"),
                "method": "put",
                "data": $("#frm-site-update").serialize(),
                "dataType": "json",
                "success": successCallback,
                "error": errorCallback
            })
        }

        function getPricesEdit() {
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
                            $(".prices-container").empty().append(
                                    $("<p>").text("Please select a correct price from below: ")
                            );
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
                        $("#btn-edit-site").show();
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

        function updateEditSiteModelButtonStatus(el) {
            var siteURL = $(el).val();
            if (siteURL != "{{$site->site_url}}") {
                $("#btn-check-price").show();
                $("#btn-edit-site").hide();
                $("#btn-report-error").hide();
            } else {
                $("#btn-check-price").hide();
                $("#btn-edit-site").show();
                $("#btn-report-error").show();
            }
        }
    </script>
</div>
