<style>
    .btn-product-dragger i {
        font-size: 20px;
    }

    .product-name-link {
        font-size: 18px;
        line-height: 46px;
    }
</style>
<table class="table table-condensed product-wrapper" data-product-id="{{$product->getKey()}}"
       data-alert-link="{{$product->urls['alert']}}"
       data-report-task-link="{{$product->urls['report_task']}}">
    <thead>
    <tr>
        <th class="shrink product-th">
            <a class="btn-collapse btn-product-dragger" href="#product-{{$product->getKey()}}" role="button"
               data-toggle="collapse"
               data-parent="#accordion" aria-expanded="true" aria-controls="product-{{$product->getKey()}}">
                <i class="glyphicon glyphicon-menu-hamburger"></i>
            </a>
        </th>
        <th class="product-th">
            <a class="text-muted product-name-link" href="#product-{{$product->getKey()}}" role="button"
               data-toggle="collapse"
               data-parent="#accordion" aria-expanded="true" aria-controls="product-{{$product->getKey()}}">
                {{$product->product_name}}
            </a>
            {!! Form::model($product, array('route' => array('product.update', $product->getKey()), 'method'=>'delete', 'class'=>'frm-edit-product', 'onsubmit' => 'submitEditProductName(this); return false;', 'style'=>'display: none;')) !!}
            <div class="input-group sl-input-group">
                <input type="text" name="product_name" placeholder="Product Name"
                       class="form-control sl-form-control input-lg product-name"
                       value="{{$product->product_name}}">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-flat btn-lg">
                        <i class="fa fa-pencil"></i>
                    </button>
                </span>
            </div>
            {!! Form::close() !!}

            <span class="btn-edit btn-edit-product" onclick="toggleEditProductName(this)">Edit &nbsp; <i
                        class="fa fa-pencil-square-o"></i></span>
        </th>
        <th class="text-right action-cell product-th">
            <a href="#" class="btn-action" onclick="showProductChart('{{$product->urls['chart']}}'); return false;"
               data-toggle="tooltip" title="chart">
                <i class="fa fa-line-chart"></i>
            </a>
            <a href="#" class="btn-action btn-alert" onclick="showProductAlertForm(this); return false;"
               data-toggle="tooltip" title="alert">
                <i class="fa {{!is_null($product->alert) ? "fa-bell alert-enabled" : "fa-bell-o"}}"></i>
            </a>
            <a href="#" class="btn-action" onclick="showProductReportTaskForm(this); return false;"
               data-toggle="tooltip" title="report">
                <i class="fa {{!is_null($product->reportTask) ? "fa-envelope text-success" : "fa-envelope-o"}}"></i>
            </a>
            {!! Form::model($product, array('route' => array('product.destroy', $product->getKey()), 'method'=>'delete', 'class'=>'frm-delete-product', 'onsubmit' => 'return false;')) !!}
            <a href="#" class="btn-action" data-name="{{$product->product_name}}"
               onclick="btnDeleteProductOnClick(this); return false;"
               data-toggle="tooltip" title="delete">
                <i class="glyphicon glyphicon-trash"></i>
            </a>
            {!! Form::close() !!}
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td colspan="2" class="table-container">
            <div id="product-{{$product->getKey()}}" class="collapsible-product-div collapse in" aria-expanded="true">
                <table class="table table-striped table-condensed tbl-site">
                    <thead>
                    <tr>
                        <th width="15%">Site</th>
                        <th width="10%" class="text-right">Current Price</th>
                        <th width="10%" class="text-right">Previous Price</th>
                        <th width="10%" class="hidden-xs text-right">Change</th>
                        <th width="10%" class="hidden-xs" style="padding-left: 20px;">Changed</th>
                        <th class="text-center" width="10%">My Price</th>
                        <th width="15%">Updated</th>
                        <th width="15%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--sites here--}}
                    @if(!is_null($product->sites))
                        @foreach($product->sites()->orderBy('my_price', 'desc')->orderBy('site_order', 'asc')->get() as $site)
                            @include('products.site.partials.single_site')
                        @endforeach
                    @endif
                    {{--sites here--}}
                    <tr class="add-site-row">
                        <td colspan="8">

                            <div class="add-item-block add-site-container"
                                 onclick="appendCreateSiteBlock(this); event.stopPropagation(); return false;">
                                <div class="add-item-label add-site-label">
                                    <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;
                                    <div class="site-label-text-container">
                                        <div>Add the product page URL of the price you want to watch.</div>
                                        <div>For example http://www.company.com.au/productpage/price</div>
                                    </div>
                                </div>
                                <div class="add-item-controls">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-7 col-sm-5 col-xs-4">
                                            <form action="{{route('site.store')}}" method="post"
                                                  class="frm-store-site"
                                                  onsubmit="getPricesCreate(this); return false;">
                                                <input type="text"
                                                       placeholder="e.g. http://www.company.com.au/productpage/price"
                                                       name="site_url"
                                                       class="txt-site-url form-control txt-item">
                                            </form>
                                        </div>
                                        <div class="col-lg-4 col-md-5 col-sm-7 col-xs-8 text-right">
                                            <button class="btn btn-primary"
                                                    onclick="getPricesCreate(this); event.stopPropagation(); event.preventDefault();">
                                                ADD SITE
                                            </button>
                                            &nbsp;&nbsp;
                                            <button class="btn btn-default btn-cancel-add-site"
                                                    id="btn-cancel-add-site-{{$product->getKey()}}"
                                                    onclick="cancelAddSite(this); event.stopPropagation(); event.preventDefault();">
                                                CANCEL
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
    <script type="text/javascript">
        var siteDrake{{$product->getKey()}} = null;

        $(function () {

            /**
             * drag and drop
             */
            siteDrake{{$product->getKey()}} = dragula([$("#product-{{$product->getKey()}} > table > tbody").get(0)], {
                moves: function (el, container, handle) {
                    return !$(handle).hasClass("add-site-row") && $(handle).closest(".add-site-row").length == 0;
                }
            }).on('drop', function (el, target, source, sibling) {
                updateSiteOrder({{$product->getKey()}});
            });

            updateProductEmptyMessage();
        });

        /**
         * set order number to element
         * @param product_id
         */
        function assignSiteOrderNumber(product_id) {
            $(".product-wrapper").filter(function () {
                return $(this).attr("data-product-id") == product_id;
            }).find(".site-wrapper").each(function (index) {
                $(this).attr("data-order", index + 1);
            });
        }

        /**
         * Send order number to server
         * @param product_id
         */
        function updateSiteOrder(product_id) {
            assignSiteOrderNumber(product_id);
            var orderList = [];
            $(".product-wrapper").filter(function () {
                return $(this).attr("data-product-id") == product_id;
            }).find(".site-wrapper").filter(function () {
                return !$(this).hasClass("gu-mirror");
            }).each(function () {
                if ($(this).attr("data-site-id")) {
                    var siteId = $(this).attr("data-site-id");
                    var siteOrder = parseInt($(this).attr("data-order"));
                    orderList.push({
                        "site_id": siteId,
                        "site_order": siteOrder
                    });
                }
            });
            $.ajax({
                "url": "{{route('site.order')}}",
                "method": "put",
                "data": {
                    "order": orderList
                },
                "dataType": "json",
                "success": function (response) {
                    if (response.status == false) {
                        alertP("Error", "Unable to update site order, please try again later.");
                    } else {
                        gaMoveSite();
                    }
                },
                "error": function (xhr, status, error) {
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        /**
         * enable add site
         * @param el
         */
        function appendCreateSiteBlock(el) {
            $(el).find(".add-item-label").slideUp();
            $(el).find(".add-item-controls").slideDown();
            $(el).find(".txt-site-url").focus();
        }

        /**
         * disable add site
         * @param el
         */
        function cancelAddSite(el) {
            $(el).closest(".add-item-block").find(".add-item-label").slideDown();
            $(el).closest(".add-item-block").find(".add-item-controls").slideUp();
            $(el).closest(".add-item-block").find(".add-item-controls input").val("");
        }


        function getPricesCreate(el) {
            var $addItemControls = $(el).closest(".add-item-controls");
            var $txtSiteURL = $addItemControls.find(".txt-site-url");
            var productID = $(el).closest(".product-wrapper").attr("data-product-id");
            showLoading();
            $.ajax({
                "url": "{{route("site.prices")}}",
                "method": "get",
                "data": {
                    "site_url": $txtSiteURL.val()
                },
                "dataType": "json",
                "success": function (response) {
                    if (typeof response.errors == 'undefined') {
                        if ((typeof response.sites == 'undefined' || response.sites.length == 0) && typeof response.targetDomain == 'undefined') {
                            addSite({
                                "site_url": $txtSiteURL.val(),
                                "product_id": productID
                            }, function (add_site_response) {
                                if (add_site_response.status == true) {
                                    loadSingleSite(add_site_response.site.urls.show, function (html) {
                                        $(el).closest(".tbl-site").find("tbody").prepend(html);
                                        cancelAddSite($addItemControls.find(".btn-cancel-add-site").get(0));
                                        updateProductEmptyMessage();
                                    });
                                } else {
                                    alertP("Error", "Unable to add site, please try again later.");
                                }
                            })
                        } else {
                            showLoading();
                            $.ajax({
                                "url": "{{route("site.prices")}}",
                                "method": "get",
                                "data": {
                                    "site_url": $txtSiteURL.val()
                                },
                                "success": function (html) {
                                    hideLoading();
                                    var $modal = $(html);
                                    $modal.modal();
                                    $modal.on("shown.bs.modal", function () {
                                        if ($.isFunction(modalReady)) {
                                            modalReady({
                                                "callback": function (addSiteData) {
                                                    addSite({
                                                        "site_url": $txtSiteURL.val(),
                                                        "domain_id": addSiteData.domain_id,
                                                        "product_id": productID
                                                    }, function (add_site_response) {
                                                        if (add_site_response.status == true) {
                                                            loadSingleSite(add_site_response.site.urls.show, function (html) {
                                                                $(el).closest(".tbl-site").find("tbody").prepend(html);
                                                                cancelAddSite($addItemControls.find(".btn-cancel-add-site").get(0));
                                                                updateProductEmptyMessage();
                                                            });
                                                        } else {
                                                            alertP("Error", "Unable to add site, please try again later.");
                                                        }
                                                        /*TODO big pb*/
                                                    });
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
                    } else {
                        var errorMsg = "Unable to add site. ";
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

        function addSite(data, callback) {
            showLoading();
            $.ajax({
                "url": "{{route('site.store')}}",
                "method": "post",
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

        function loadSingleSite(url, callback) {
            showLoading();
            $.ajax({
                "url": url,
                "method": "get",
                "success": function (html) {
                    hideLoading();

                    if ($.isFunction(callback)) {
                        callback(html);
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }


        function btnDeleteProductOnClick(el) {
            confirmP("Delete Product", "Are you sure you want to delete the " + $(el).attr("data-name") + " Product?", {
                "affirmative": {
                    "text": "Delete",
                    "class": "btn-danger btn-flat",
                    "dismiss": true,
                    "callback": function () {
                        var $form = $(el).closest(".frm-delete-product");
                        showLoading();
                        $.ajax({
                            "url": $form.attr("action"),
                            "method": "delete",
                            "data": $form.serialize(),
                            "dataType": "json",
                            "success": function (response) {
                                hideLoading();
                                if (response.status == true) {
                                    gaDeleteProduct();
                                    alertP("Delete Product", "Product has been deleted.");
                                    $(el).closest(".product-wrapper").remove();
                                } else {
                                    alertP("Error", "Unable to delete product, please try again later.");
                                }
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

        function toggleEditProductName(el) {
            var $tbl = $(el).closest(".product-wrapper")
            if ($(el).hasClass("editing")) {
                $(el).removeClass("editing");
                $tbl.find(".product-name-link").show();
                $tbl.find(".frm-edit-product").hide();
            } else {
                $tbl.find(".product-name-link").hide();
                $tbl.find(".frm-edit-product").show();
                $(el).addClass("editing");
            }
        }

        function submitEditProductName(el) {
            showLoading();
            $.ajax({
                "url": $(el).attr("action"),
                "method": "put",
                "data": $(el).serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        gaEditProduct();

                        alertP("Update Product", "Product name has been updated.");
                        $(el).siblings(".product-name-link").text($(el).find(".product-name").val()).show();
                        $(el).hide();
                        $(el).closest(".product-wrapper").find(".btn-action.editing").removeClass("editing");
                    } else {
                        var errorMsg = "Unable to update product. ";
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
            });
        }

        function showProductAlertForm(el) {
            showLoading();
            var productID = $(el).closest(".product-wrapper").attr("data-product-id");

            $.ajax({
                "url": $(el).closest(".product-wrapper").attr("data-alert-link"),
                "method": "get",
                "data": {
                    "product_id": productID
                },
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
                        $("#modal-alert-product").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }

        function showProductChart(url) {
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

        function showProductReportTaskForm(el) {
            showLoading();
            $.ajax({
                "url": $(el).closest(".product-wrapper").attr("data-report-task-link"),
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
                                        $(el).find("i").removeClass().addClass("fa fa-envelope text-success");
                                    }
                                },
                                "deleteCallback": function (response) {
                                    if (response.status == true) {
                                        $(el).find("i").removeClass().addClass("fa fa-envelope-o");
                                    }
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $("#modal-report-task-product").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }

        function updateProductEmptyMessage(el) {
            function updateSingleProductEmptyMessage(el) {
                var $tblSite = null;
                if ($(el).hasClass("tbl-site")) {
                    $tblSite = $(el);
                } else {
                    $tblSite = $(el).find(".tbl-site");
                }

                var $bodyRow = $tblSite.find("tbody > tr").filter(function () {
                    return !$(this).hasClass("empty-message-row") && !$(this).hasClass("add-site-row")
                });
                console.info('$bodyRow', $bodyRow);
                if ($bodyRow.length == 0) {
                    $tblSite.find(".empty-message-row").remove();
                    $tblSite.find("tbody").prepend(
                            $("<tr>").addClass("empty-message-row").append(
                                    $("<td>").attr({
                                        "colspan": 8
                                    }).addClass("text-center").text("To start tracking prices, simply copy and paste the URL of the product page of the website your want to track.")
                            )
                    )
                } else {
                    $tblSite.find(".empty-message-row").remove();
                }
            }

            if (typeof el != 'undefined') {
                updateSingleProductEmptyMessage(el);
            } else {
                $(".tbl-site").each(function () {
                    updateSingleProductEmptyMessage(this);
                })
            }
        }
    </script>
</table>