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
                {!! Form::open(array('route' => array('site.store'), 'method'=>'post', "onsubmit"=>"return false", "id"=>"frm-site-store")) !!}
                <input type="hidden" name="product_id" value="{{$product->getKey()}}">
                <div class="form-group required">
                    {!! Form::label('site_url', 'URL', array('class' => 'control-label')) !!}
                    &nbsp;
                    <a href="#" class="text-muted" data-toggle="popover" style="font-size: 16px; font-weight: bold;"
                       data-placement="right" onclick="return false;" data-trigger="hover"
                       data-content="Add the URL for the product you wish to track by going to the product's webpage, copying the URL from the address bar of your browser and pasting it in this field.">
                        <i class="fa fa-question-circle"></i>
                    </a>
                    {!! Form::text('site_url', null, array('class' => 'form-control m-b-5', 'id'=>'txt-site-url', 'placeholder' => 'Enter or copy URL')) !!}
                </div>
                <div class="prices-container" style="display: none;">
                    <p>Please select a correct price from below: </p>
                </div>

                <div class="report-error-container" style="display: none;">
                </div>

                {!! Form::close() !!}

            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-check-price">Check Price</button>
                <button class="btn btn-primary btn-flat" id="btn-create-site" style="display: none;">OK</button>
                <button class="btn btn-warning btn-flat" id="btn-report-error" style="display: none;">Error</button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#txt-site-url").on("input", function () {
                $(".report-error-container").slideUp(function () {
                    $(this).html("");
                });
                $("#btn-check-price").show();
                $("#btn-create-site").hide();
                $("#btn-report-error").hide();
                $(".prices-container").empty().append(
                        $("<p>").text("Please select a correct price from below: ")
                ).slideUp();
            });

            $("[data-toggle=popover]").popover();

            $("#btn-create-site").on("click", function () {
                if (!$(".prices-container").is(":visible")) {
                    $(".rad-site-id").prop("checked", false);
                    if ($("#txt-comment").length > 0 && $("#txt-comment").val() == "") {
                        alertP("Oops! Something went wrong.", "Please describe the location of the price in the web page.");
                        return false;
                    }
                } else {
                    if ($("#txt-comment").length == 0 && $(".prices-container input[type=radio]:checked").length == 0) {
                        alertP("Oops! Something went wrong.", "Please select a correct price from the list");
                        return false;
                    }
                }
                showLoading();


                submitSiteStore(function (response) {
                    hideLoading();
                    if (response.status == true) {
                        gaAddSite();

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
                            alertP("Oops! Something went wrong.", "Unable to add site, please try again later.");
                        }
                    }
                }, function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                });
            });
            $("#btn-check-price").on("click", function () {
                getPricesCreateModal();
            });

            $("#btn-report-error").on("click", function () {
                appendReportErrorContainer();
                $(".report-error-container").slideDown();
                $(".prices-container").slideUp();
                $(this).hide();
            });
        }

        function appendReportErrorContainer() {
            $(".report-error-container").append(
                    $("<div>").addClass("form-group required").append(
                            $("<label>").text("Please give us some hints to locate the price"),
                            $("<a>").attr({
                                "href": "#",
                                "id": "btn-close-report-error",
                                "onclick": "closeReportErrorContainerOnClick(this); return false;"
                            }).addClass("close").html("&times;"),
                            $("<textarea>").attr({
                                "name": "comment",
                                "id": "txt-comment",
                                "cols": "30",
                                "rows": "5"
                            }).addClass("form-control").css("resize", "vertical")
                    )
            )
        }

        function closeReportErrorContainerOnClick(el) {
            $(el).closest(".report-error-container").slideUp(function () {
                $(this).html("");
            });
            $(".prices-container").slideDown();
            $("#btn-report-error").show();
        }

        function getPricesCreateModal() {
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
                        if (response.sites.length > 0 || typeof response.targetDomain != "undefined") {
                            if (typeof response.targetDomain != "undefined") {
                                $(".prices-container").append(
                                        $("<div>").append(
                                                $("<label>").append(
                                                        $("<input>").attr({
                                                            "type": "radio",
                                                            "value": response.targetDomain.domain_id,
                                                            "name": "domain_id",
                                                            "onclick": "$('.rad-site-id[name=site_id]').prop('checked', false);"
                                                        }).addClass("rad-site-id"),
                                                        $("<input>").attr({
                                                            "type": "hidden",
                                                            "value": response.targetDomain.recent_price,
                                                            "name": "domain_price"
                                                        }),
                                                        $("<span>").text('$' + (parseFloat(response.targetDomain.recent_price)).formatMoney(2, '.', ','))
                                                )
                                        ).addClass("radio")
                                )
                            }
                            $.each(response.sites, function (index, site) {
                                $(".prices-container").append(
                                        $("<div>").append(
                                                $("<label>").append(
                                                        $("<input>").attr({
                                                            "type": "radio",
                                                            "value": site.site_id,
                                                            "name": "site_id",
                                                            "onclick": "$('.rad-site-id[name=domain_id]').prop('checked', false);"
                                                        }).addClass("rad-site-id"),
                                                        $("<span>").text('$' + (parseFloat(site.recent_price)).formatMoney(2, '.', ','))
                                                )
                                        ).addClass("radio")
                                )
                            });
                            $(".prices-container").show();
                            $("#btn-report-error").show();
                        } else {
                            $(".prices-container").empty().append(
                                    $("<div>").text("Price will be available soon."),
                                    $("<div>").css("margin-top", "10px").append(
                                            $("<textarea>").attr({
                                                "placeholder": "If there are multiple prices on this page, please advise the specific price required.",
                                                "cols": "30",
                                                "rows": "5",
                                                "id": "txt-comment",
                                                "name": "comment"
                                            }).addClass("form-control")
                                    )
                            );
                        }
                        $(".prices-container").show();
                        $("#btn-check-price").hide();
                        $("#btn-create-site").show();
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
                            alertP("Oops! Something went wrong.", "Unable to get price, please try again later.");
                        }
                    }
                },
                "error": function () {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
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
