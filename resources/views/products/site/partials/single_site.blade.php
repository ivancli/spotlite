<tr class="site-wrapper" data-site-id="{{$site->getKey()}}"
    data-site-edit-url="{{$site->urls['edit']}}"
    data-site-alert-url="{{$site->urls['alert']}}"
    data-site-update-my-price-url="{{$site->urls['update_my_price']}}">
    <td class="site-url">
        <a href="{{$site->site_url}}" target="_blank" class="text-muted site-url-link" data-toggle="popover"
           data-container="body"
           data-trigger="hover"
           data-content="{{$site->site_url}}">
            {{parse_url($site->site_url)['host']}}
        </a>

        <div class="frm-edit-site-url input-group sl-input-group" style="display: none;">
            <input type="text" name="site_url" placeholder="Site URL"
                   class="form-control sl-form-control txt-site-url"
                   value="{{$site->site_url}}">
            <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-flat" data-url="{{$site->urls['update']}}"
                            onclick="getPricesEdit(this); return false;">
                        <i class="fa fa-pencil"></i>
                    </button>
                </span>
        </div>

        <span class="btn-edit btn-edit-site" onclick="toggleEditSiteURL(this)">
            Edit &nbsp;
            <i class="fa fa-pencil-square-o"></i>
        </span>
    </td>
    <td>
        @if($site->status == 'invalid')
            <div class="text-right">
                <a href="#" onclick="return false;" data-toggle="popover" data-trigger="hover focus click"
                   data-content="The site you have provided is not a valid page for pricing. Please update the site with product detail page URL.">
                    <i class="fa fa-ban text-danger"></i>
                </a>
                &nbsp;
                Invalid page for pricing
            </div>
        @else
            <div class="text-right">
                @if(is_null($site->recent_price))
                    <div class="p-l-10">
                        <strong><i class="fa fa-minus"></i></strong>
                    </div>
                @else
                    {{"$" . number_format($site->recent_price, 2, '.', ',')}}
                @endif
            </div>
        @endif
    </td>
    <td>
        <div class="text-right">
            @if(!is_null($site->previousPrice))
                ${{number_format($site->previousPrice->price, 2, '.', ',')}}
            @else
                <strong><i class="fa fa-minus"></i></strong>
            @endif
        </div>
    </td>
    <td class="hidden-xs">
        <div class="text-right">
            @if(!is_null($site->diffPrice))
                @if($site->diffPrice != 0)
                    <i class="glyphicon {{$site->diffPrice > 0 ? "glyphicon-triangle-top text-success" : "glyphicon-triangle-bottom text-danger"}}"></i>
                    ${{number_format(abs($site->diffPrice), 2, '.', ',')}}
                @else
                    <div class="p-l-10">
                        <strong><i class="fa fa-minus"></i></strong>
                    </div>
                @endif
            @else
                <div class="p-l-10">
                    <strong><i class="fa fa-minus"></i></strong>
                </div>
            @endif
        </div>
    </td>
    <td class="hidden-xs" style="padding-left: 20px;">
        @if(!is_null($site->priceLastChangedAt))
            {{date(auth()->user()->preference('DATE_FORMAT') . " " . auth()->user()->preference('TIME_FORMAT'), strtotime($site->priceLastChangedAt))}}

        @else
            <div class="p-l-10">
                <strong><i class="fa fa-minus"></i></strong>
            </div>
        @endif
    </td>
    <td align="center">
        <a href="#" class="btn-my-price" onclick="toggleMyPrice(this); return false;"
           data-product-alert-on-my-price="{{is_null($site->product->alertOnMyPrice()) ? "" : "y"}}"
           data-site-alerts-on-my-price="{{$site->product->siteAlertsOnMyPrice()->count()}}">
            <i class="fa fa-check-circle-o {{$site->my_price == "y" ? "text-primary" : "text-muted-further"}}"></i>
        </a>
    </td>
    <td>
        @if(!is_null($site->last_crawled_at))
            <span title="{{date(auth()->user()->preference('DATE_FORMAT') . " " . auth()->user()->preference('TIME_FORMAT'), strtotime($site->last_crawled_at))}}"
                  data-toggle="tooltip">
                {{date(auth()->user()->preference('DATE_FORMAT'), strtotime($site->last_crawled_at))}}
                <span class="hidden-xs hidden-sm">{{date(auth()->user()->preference('TIME_FORMAT'), strtotime($site->last_crawled_at))}}</span>
            </span>
        @else
            <div class="p-l-10">
                <strong><i class="fa fa-minus"></i></strong>
            </div>
        @endif
    </td>
    <td class="text-right action-cell">
        <a href="#" class="btn-action" onclick="showSiteChart('{{$site->urls['chart']}}'); return false;"
           data-toggle="tooltip" title="chart">
            <i class="fa fa-line-chart"></i>
        </a>
        <a href="#" class="btn-action" onclick="showSiteAlertForm(this); return false;"
           data-toggle="tooltip" title="alert">
            <i class="fa {{!is_null($site->alert) ? "fa-bell alert-enabled" : "fa-bell-o"}}"></i>
        </a>
        {!! Form::model($site, array('route' => array('site.destroy', $site->getKey()), 'method'=>'delete', 'class'=>'frm-delete-site', 'onsubmit' => 'return false;')) !!}
        <a href="#" class="btn-action" data-name="{{parse_url($site->site_url)['host']}}"
           onclick="btnDeleteSiteOnClick(this); return false;"
           data-toggle="tooltip" title="delete">
            <i class="glyphicon glyphicon-trash"></i>
        </a>
        {!! Form::close() !!}
    </td>
    <script type="text/javascript">
        function btnDeleteSiteOnClick(el) {
            deletePopup("Delete Site", "Are you sure you want to delete the " + $(el).attr("data-name") + " Site?",
                    "By deleting this site, you will lose the following data:",
                    [
                        "All data related to the site you are tracking",
                        "The charts of the site",
                        "The presentation of the data"
                    ],
                    {
                        "affirmative": {
                            "text": "Delete",
                            "class": "btn-danger btn-flat",
                            "dismiss": true,
                            "callback": function () {
                                var $form = $(el).closest(".frm-delete-site");
                                showLoading();
                                $.ajax({
                                    "url": $form.attr("action"),
                                    "method": "delete",
                                    "data": $form.serialize(),
                                    "dataType": "json",
                                    "success": function (response) {
                                        hideLoading();
                                        if (response.status == true) {
                                            gaDeleteSite();
                                            alertP("Delete Site", "The site has been deleted.");
                                            updateUserSiteUsage(el);
                                            $(el).closest(".site-wrapper").remove();
                                        } else {
                                            alertP("Error", "Unable to delete site, please try again later.");
                                        }
                                        updateProductEmptyMessage();
                                    },
                                    "error": function (xhr, status, error) {
                                        hideLoading();
                                        describeServerRespondedError(xhr.status);
                                    }
                                })
                            }
                        },
                        "negative": {
                            "text": "Cancel",
                            "class": "btn-default btn-flat",
                            "dismiss": true
                        }
                    });
        }

        function toggleEditSiteURL(el) {
            var $tr = $(el).closest(".site-wrapper");
            if ($(el).hasClass("editing")) {
                $(el).removeClass("editing");
                $tr.find(".site-url-link").show();
                $tr.find(".frm-edit-site-url").hide();
            } else {
                $tr.find(".site-url-link").hide();
                $tr.find(".frm-edit-site-url").show();
                $tr.find(".frm-edit-site-url .txt-site-url").focus();
                $(el).addClass("editing");
            }
        }

        function getPricesEdit(el) {
            var $formEditSiteURL = $(el).closest(".frm-edit-site-url");
            var $txtSiteURL = $formEditSiteURL.find(".txt-site-url");
            var $siteWrapper = $(el).closest(".site-wrapper");
            var siteID = $siteWrapper.attr("data-site-id");
            showLoading();
            $.ajax({
                "url": "{{route("site.prices")}}",
                "method": "get",
                "data": {
                    "site_url": $txtSiteURL.val(),
                    "site_id": siteID
                },
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (typeof response.errors == 'undefined') {
                        //PRICE NOT FOUND
                        if ((typeof response.sites == 'undefined' || response.sites.length == 0) && typeof response.targetDomain == 'undefined') {
                            editSite({
                                "site_url": $txtSiteURL.val(),
                                "url": $(el).attr("data-url")
                            }, function (edit_site_response) {
                                if (edit_site_response.status == true) {
                                    loadSingleSite(edit_site_response.site.urls.show, function (html) {
                                        toggleEditSiteURL($(el).closest(".site-wrapper").find("btn-edit-site").get(0));
                                        $(el).closest(".site-wrapper").replaceWith(html);
                                        updateProductEmptyMessage();
                                    });
                                } else {
                                    alertP("Error", "Unable to add site, please try again later.");
                                }
                            })
                        }
                        //PRICE FOUND
                        else {
                            showLoading();
                            showSelectPricePopup({
                                "site_url": $txtSiteURL.val()
                            }, function (editSiteData) {
                                editSite({
                                    "site_url": $txtSiteURL.val(),
                                    "domain_id": editSiteData.domain_id,
                                    "site_id": editSiteData.site_id,
                                    "url": $(el).attr("data-url")
                                }, function (edit_site_response) {
                                    if (edit_site_response.status == true) {
                                        loadSingleSite(edit_site_response.site.urls.show, function (html) {
                                            toggleEditSiteURL($(el).closest(".site-wrapper").find("btn-edit-site").get(0));
                                            $(el).closest(".site-wrapper").replaceWith(html);
                                            updateProductEmptyMessage();
                                        });
                                    } else {
                                        alertP("Error", "Unable to add site, please try again later.");
                                    }
                                });
                            });
                        }
                    } else {
                        var errorMsg = "Unable to edit site. ";
                        if (response.errors != null) {
                            $.each(response.errors, function (index, error) {
                                errorMsg += error + " ";
                            })
                        }
                        alertP("Error", errorMsg);
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function editSite(data, callback) {
            showLoading();
            $.ajax({
                "url": data.url,
                "method": "put",
                "data": data,
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

        function showSelectPricePopup(data, callback) {
            showLoading();
            $.ajax({
                "url": "{{route("site.prices")}}",
                "method": "get",
                "data": data,
                "success": function (html) {
                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "callback": function (response) {
                                    if ($.isFunction(callback)) {
                                        callback(response);
                                    }
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $("#modal-site-prices").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }


        function showSiteAlertForm(el) {
            showLoading();
            $.ajax({
                "url": $(el).closest(".site-wrapper").attr("data-site-alert-url"),
                "method": "get",
                "success": function (html) {
                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "updateCallback": function (response) {
                                    if (response.status == true) {
                                        $(el).find("i").removeClass().addClass("fa fa-bell alert-enabled");
                                    }
                                },
                                "deleteCallback": function (response) {
                                    if (response.status == true) {
                                        $(el).find("i").removeClass().addClass("fa fa-bell-o");
                                    }
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $("#modal-alert-site").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }

        function toggleMyPrice(el) {
            if (($(el).attr("data-product-alert-on-my-price") == 'y' || $(el).attr("data-site-alerts-on-my-price") > 0) && $(el).find("i").hasClass("text-primary")) {
                deletePopup("My Price", "Do you want to disable 'My Price'?",
                        "By updating my price, you will lose the following data:",
                        [
                            "My Price related alerts"
                        ],
                        {
                            "affirmative": {
                                "text": "Delete",
                                "class": "btn-danger btn-flat",
                                "dismiss": true,
                                "callback": function () {
                                    submitToggleMyPrice(el);
                                }
                            },
                            "negative": {
                                "text": "Cancel",
                                "class": "btn-default btn-flat",
                                "dismiss": true
                            }
                        });
            } else {
                submitToggleMyPrice(el);
            }
        }

        function submitToggleMyPrice(el) {
            var myPrice = $(el).find("i").hasClass("text-primary") ? "n" : "y";
            showLoading();

            $.ajax({
                "url": $(el).find("i").closest(".site-wrapper").attr("data-site-update-my-price-url"),
                "method": "put",
                "data": {
                    "my_price": myPrice
                },
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        gaSetMyPrice();
                        showLoading();
                        $.ajax({
                            "url": '{{$site->product->urls['show']}}',
                            "method": "get",
                            "success": function (html) {
                                hideLoading();
                                $(el).closest(".product-wrapper").replaceWith(html);
                            },
                            "error": function (xhr, status, error) {
                                hideLoading();
                                describeServerRespondedError(xhr.status);
                            }
                        });
                    } else {
                        alertP("Error", "unable to set my price, please try again later.");
                    }
                },
                "error": function () {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function showSiteChart(url) {
            showLoading();
            $.ajax({
                "url": url,
                "method": "get",
                "success": function (html) {
                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady()
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $(this).remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }

        function initPopover() {
            $("[data-toggle=popover]").popover();
        }

        $(function () {
            initPopover();
        })
    </script>
</tr>