<tr class="site-wrapper" data-product-site-id="{{$productSite->getKey()}}"
    data-site-edit-url="{{$productSite->urls['edit']}}"
    data-site-alert-url="{{$productSite->urls['alert']}}"
    data-site-update-url="{{$productSite->urls['update']}}">
    <td>
        <a href="{{$productSite->site->site_url}}" target="_blank" class="text-muted" data-toggle="tooltip" data-trigger="click"
           title="{{$productSite->site->site_url}}">
            {{parse_url($productSite->site->site_url)['host']}}
        </a>
    </td>
    {{--<td class="hidden-sm hidden-xs" style="padding-right: 10px;">--}}
    {{--<a href="{{$productSite->site->site_url}}" target="_blank" class="text-muted">--}}
    {{--{{parse_url($productSite->site->site_url)['path']}}--}}
    {{--</a>--}}
    {{--</td>--}}
    <td>
        {{is_null($productSite->site->recent_price) ? '' : "$" . number_format($productSite->site->recent_price, 2, '.', ',')}}
    </td>
    <td class="text-center">
        @if(!is_null($productSite->site->recent_price))
            @if(!is_null($productSite->site->price_diff) && $productSite->site->price_diff != 0)
                <i class="glyphicon {{$productSite->site->price_diff > 0 ? "glyphicon-triangle-top text-success" : "glyphicon-triangle-bottom text-danger"}}"></i>
                ${{number_format(abs($productSite->site->price_diff), 2, '.', ',')}}
            @else
                -
            @endif
        @endif
    </td>
    <td align="center">

        <a href="#" class="btn-my-price" onclick="toggleMyPrice(this); return false;"
           data-alert-is-subjected-my-price="{{$productSite->alert['comparison_price_type'] == 'my price' ? 'y' : 'n'}}">
            <i class="fa fa-check-circle-o {{$productSite->my_price == "y" ? "text-primary" : "text-muted-further"}}"></i>
        </a>
    </td>
    <td>
        @if(!is_null($productSite->site->last_crawled_at))
            <div title="{{$productSite->site->last_crawled_at}}" data-toggle="tooltip">
                {{date("Y-m-d", strtotime($productSite->site->last_crawled_at))}}
                <span class="hidden-xs hidden-sm">{{date("H:i:s", strtotime($productSite->site->last_crawled_at))}}</span>
            </div>
        @endif
    </td>
    <td class="text-right action-cell">
        <a href="#" class="btn-action" onclick="showProductSiteChart('{{$productSite->urls['chart']}}'); return false;"
           data-toggle="tooltip" title="chart">
            <i class="fa fa-line-chart"></i>
        </a>
        <a href="#" class="btn-action" onclick="showSiteAlertForm(this); return false;"
           data-toggle="tooltip" title="alert">
            <i class="fa {{!is_null($productSite->alert) ? "fa-bell alert-enabled" : "fa-bell-o"}}"></i>
        </a>
        <a href="#" class="btn-action" onclick="btnEditSiteOnClick(this); return false;"
           data-toggle="tooltip" title="edit">
            <i class="fa fa-pencil-square-o"></i>
        </a>

        {{--TODO not yet finished--}}
        {{--change the submitting parameters and update the product site controller destroy function--}}
        {!! Form::model($productSite, array('route' => array('product_site.destroy', $productSite->getKey()), 'method'=>'delete', 'class'=>'frm-delete-site', 'onsubmit' => 'return false;')) !!}
        {{--<input type="hidden" name="product_site_id" value="{{$site->pivot->product_site_id}}">--}}
        <a href="#" class="btn-action" onclick="btnDeleteSiteOnClick(this); return false;"
           data-toggle="tooltip" title="delete">
            <i class="glyphicon glyphicon-trash text-danger"></i>
        </a>
        {!! Form::close() !!}
    </td>
    <script type="text/javascript">
        function btnDeleteSiteOnClick(el) {
            confirmP("Delete Site", "Do you want to delete this site?", {
                "affirmative": {
                    "text": "Delete",
                    "class": "btn-danger",
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
                    "class": "btn-default",
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
                    "product_site_id": $(el).closest(".site-wrapper").attr("data-product-site-id")
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
                                        if (typeof response.productSite != 'undefined') {
                                            $.get(response.productSite.urls.show, function (html) {
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
                        $("#modal-alert-product-site").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to show edit alert form, please try again later.");
                }
            });
        }

        function toggleMyPrice(el) {
            if ($(el).attr("data-alert-is-subjected-my-price") == 'y' && !$(el).find("i").hasClass("text-primary")) {
                confirmP("My Price", "The alert of this site is subjected to 'My Price'. Setting this site to be 'My Price' will disable the alert. Do you want to set this site as 'My Price'?", {
                    "affirmative": {
                        "text": "Yes",
                        "class": "btn-primary",
                        "dismiss": true,
                        "callback": function () {
                            submitToggleMyPrice(el);
                        }
                    },
                    "negative": {
                        "text": "Cancel",
                        "class": "btn-default",
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
                        if ($(el).find("i").hasClass("text-primary")) {
                            $(el).find("i").removeClass("text-primary").addClass("text-muted-further")
                        } else {
                            $(el).closest(".product-wrapper").find(".btn-my-price i").removeClass("text-primary").addClass("text-muted-further")
                            $(el).find("i").removeClass("text-muted-further").addClass("text-primary")

                        }
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

        function showProductSiteChart(url) {
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
    </script>
</tr>