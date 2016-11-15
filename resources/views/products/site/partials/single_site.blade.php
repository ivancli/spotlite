<tr class="site-wrapper" data-site-id="{{$site->getKey()}}"
    data-site-edit-url="{{$site->urls['edit']}}"
    data-site-alert-url="{{$site->urls['alert']}}"
    data-site-update-url="{{$site->urls['update']}}">
    <td class="site-url">
        <a href="{{$site->site_url}}" target="_blank" class="text-muted" data-toggle="popover"
           data-trigger="hover"
           data-content="{{$site->site_url}}">
            {{parse_url($site->site_url)['host']}}
        </a>
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
        <a href="#" class="btn-action" onclick="btnEditSiteOnClick(this); return false;"
           data-toggle="tooltip" title="edit">
            <i class="fa fa-pencil-square-o"></i>
        </a>

        {{--TODO not yet finished--}}
        {{--change the submitting parameters and update the site controller destroy function--}}
        {!! Form::model($site, array('route' => array('site.destroy', $site->getKey()), 'method'=>'delete', 'class'=>'frm-delete-site', 'onsubmit' => 'return false;')) !!}
        <a href="#" class="btn-action" data-name="{{parse_url($site->site_url)['host']}}"
           onclick="btnDeleteSiteOnClick(this); return false;"
           data-toggle="tooltip" title="delete">
            <i class="glyphicon glyphicon-trash text-danger"></i>
        </a>
        {!! Form::close() !!}
    </td>
    <script type="text/javascript">
        function btnDeleteSiteOnClick(el) {
            confirmP("Delete Site", "Are you sure you want to delete the " + $(el).attr("data-name") + " Site?", {
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
                                    $(el).closest(".site-wrapper").remove();
                                } else {
                                    alertP("Error", "Unable to delete site, please try again later.");
                                }
                            },
                            "error": function (xhr, status, error) {
                                hideLoading();
                                alertP("Error", "Unable to delete site, please try again later.");
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

        function btnEditSiteOnClick(el) {
            showLoading();
            $.ajax({
                "url": $(el).closest(".site-wrapper").attr("data-site-edit-url"),
                "method": "get",
                "data": {
                    "site_id": $(el).closest(".site-wrapper").attr("data-site-id")
                },
                "success": function (html) {
                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "callback": function (response) {
                                    if (response.status == true) {
                                        showLoading();
                                        if (typeof response.site != 'undefined') {
                                            $.get(response.site.urls.show, function (html) {
                                                hideLoading();
                                                $(el).closest(".site-wrapper").replaceWith(html);
                                            });
                                        }
                                    } else {
                                        alertP("Unable to edit this site, please try again later.");
                                    }
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $("#modal-site-update").remove();
                    });
                },
                "error": function () {
                    hideLoading();
                    alertP("Error", "Unable to edit this site, please try again later.");
                }
            })
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
                    alertP("Error", "Unable to show edit alert form, please try again later.");
                }
            });
        }

        function toggleMyPrice(el) {
            if (($(el).attr("data-product-alert-on-my-price") == 'y' || $(el).attr("data-site-alerts-on-my-price") > 0) && $(el).find("i").hasClass("text-primary")) {
                confirmP("My Price", "The alerts of product or other sites are subjected to 'My Price'. Disabling 'My Price' will remove the related alerts. Do you want to disable 'My Price'?", {
                    "affirmative": {
                        "text": "Yes",
                        "class": "btn-primary btn-flat",
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
                "url": $(el).find("i").closest(".site-wrapper").attr("data-site-update-url"),
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
                        $.get('{{$site->product->urls['show']}}', function (html) {
                            hideLoading();
                            $(el).closest(".product-wrapper").replaceWith(html);
                        });
                    } else {
                        alertP("Error", "unable to set my price, please try again later.");
                    }
                },
                "error": function () {
                    hideLoading();
                    alertP("Error", "unable to set my price, please try again later.");
                }
            })
        }

        function showSiteChart(url) {
            showLoading();
            $.get(url, function (html) {
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