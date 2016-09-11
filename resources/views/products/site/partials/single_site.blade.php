<tr class="site-wrapper" data-product-site-id="{{$productSite->getKey()}}"
    data-site-edit-url="{{$productSite->urls['edit']}}"
    data-site-alert-url="{{$productSite->urls['alert']}}">
    <td>{{parse_url($productSite->site->site_url)['host']}}</td>
    <td>{{is_null($productSite->site->recent_price) ? '' : "$" . number_format($productSite->site->recent_price, 2, '.', ',')}}</td>
    <td></td>
    <td>{{$productSite->site->last_crawled_at}}</td>
    <td class="text-right action-cell">
        <a href="#" class="btn-action" onclick="showSiteAlertForm(this); return false;">
            <i class="fa {{!is_null($productSite->alert) ? "fa-bell alert-enabled" : "fa-bell-o"}}"></i>
        </a>
        <a href="#" class="btn-action" onclick="btnEditSiteOnClick(this); return false;">
            <i class="fa fa-pencil-square-o"></i>
        </a>

        {{--TODO not yet finished--}}
        {{--change the submitting parameters and update the product site controller destroy function--}}
        {!! Form::model($productSite, array('route' => array('product_site.destroy', $productSite->getKey()), 'method'=>'delete', 'class'=>'frm-delete-site', 'onsubmit' => 'return false;')) !!}
        {{--<input type="hidden" name="product_site_id" value="{{$site->pivot->product_site_id}}">--}}
        <a href="#" class="btn-action" onclick="btnDeleteSiteOnClick(this); return false;">
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
                    $modal.modal({
                        "backdrop": "static",
                        "keyboard": false
                    });
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "callback": function (response) {
                                    if (response.status == true) {
                                        showLoading();
                                        console.info('response.productSite', response.productSite);
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
                    $modal.modal({
                        "backdrop": "static",
                        "keyboard": false
                    });
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
                    alertP("Error", "Unable to show add site form, please try again later.");
                }
            });
        }
    </script>
</tr>