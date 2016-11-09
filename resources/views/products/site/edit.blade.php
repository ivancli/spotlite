<div class="modal fade" tabindex="-1" role="dialog" id="modal-site-update">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{$site->product->product_name}}</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::model($site, array('route' => array('site.update', $site->getKey()), 'method'=>'put', "onsubmit"=>"return false", "id"=>"frm-site-update")) !!}
                <div class="form-group required">
                    {!! Form::label('site_url', 'URL', array('class' => 'control-label')) !!}
                    &nbsp;
                    <a href="#" class="text-muted" data-toggle="popover" style="font-size: 16px; font-weight: bold;"
                       data-placement="right" onclick="return false;" data-trigger="hover"
                       data-content="Add the URL for the product you wish to track by going to the product's webpage, copying the URL from the address bar of your browser and pasting it in this field.">
                        <i class="fa fa-question-circle"></i>
                    </a>
                    {!! Form::text('site_url', null, array('class' => 'form-control', 'id'=>'txt-site-url', 'oninput'=>'updateEditSiteModelButtonStatus(this)', 'placeholder'=>'Enter or copy URL')) !!}
                </div>

                @if(!is_null($site->recent_price))
                    <p>Current price: ${{number_format($site->recent_price, 2, '.', ',')}}</p>
                @endif
                <div class="prices-container">
                    @if(isset($sites) && $sites->count() > 0 || isset($targetDomain))
                        <p>Please select a correct price from below: </p>
                        @if(isset($targetDomain) && $targetDomain['recent_price'] != $site->recent_price)
                            <div class="radio">
                                <label>
                                    <input type="radio" name="domain_id" class="rad-site-id"
                                           value="{{$targetDomain['domain_id']}}"
                                           onclick="$('.rad-site-id[name=site_id]').prop('checked', false);"
                                    >
                                    <input type="hidden" name="domain_price" value="{{$targetDomain['recent_price']}}">
                                    ${{number_format($targetDomain['recent_price'], 2, '.', ',')}}

                                </label>
                            </div>
                        @endif
                        @foreach($sites as $priceSite)
                            <div class="radio">
                                <label>
                                    <input type="radio" name="site_id" class="rad-site-id"
                                           value="{{$priceSite->getKey()}}"
                                           {{$priceSite->getKey() == $site->getKey() ? 'checked="checked"' : ""}}
                                           onclick="$('.rad-site-id[name=domain_id]').prop('checked', false);">
                                    ${{number_format($priceSite->recent_price, 2, '.', ',')}}
                                </label>
                            </div>
                        @endforeach
                    @else
                        <p>Price will be available soon.</p>
                    @endif
                </div>

                <div class="report-error-container" style="display: none;">
                </div>

                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-check-price" style="display: none;">Check Price</button>
                <button class="btn btn-primary" id="btn-edit-site">OK</button>

                <button class="btn btn-warning" id="btn-report-error"
                        style="{{(!isset($sites) || $sites->count() == 0) && !isset($targetDomain) ? 'display: none;' : ""}}">Error
                </button>
                <button data-dismiss="modal" class="btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("[data-toggle=popover]").popover();

            $("#btn-edit-site").on("click", function () {
                if (!$(".prices-container").is(":visible")) {
                    $(".rad-site-id").prop("checked", false);

                    if ($("#txt-comment").val() == "") {
                        alertP("Error", "Please describe the location of the price in the web page.");
                        return false;
                    }
                }
                showLoading();
                submitSiteUpdate(function (response) {
                    hideLoading();
                    if (response.status == true) {
                        gaEditSite();

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

        function closeReportErrorContainerOnClick(el){
            $(el).closest(".report-error-container").slideUp(function(){
                $(this).html("");
            });
            $(".prices-container").slideDown();
            $("#btn-report-error").show();
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
                        if (response.sites.length > 0 || typeof response.targetDomain != 'undefined') {
                            $(".prices-container").empty().append(
                                    $("<p>").text("Please select a correct price from below: ")
                            );
                            if (typeof response.targetDomain != "undefined") {
                                $(".prices-container").append(
                                        $("<div>").append(
                                                $("<label>").append(
                                                        $("<input>").attr({
                                                            "type": "radio",
                                                            "value": response.targetDomain.domain_id,
                                                            "name": "domain_id"
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
                        $("#btn-edit-site").show();
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
                            alertP("Error", "Unable to get price, please try again later.");
                        }
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
