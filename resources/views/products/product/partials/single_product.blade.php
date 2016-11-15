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
                       class="form-control sl-form-control input-sm product-name"
                       value="{{$product->product_name}}">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-flat btn-sm">
                        <i class="fa fa-pencil"></i>
                    </button>
                </span>
            </div>
            {!! Form::close() !!}
            &nbsp;
            <button class="btn btn-primary btn-xs btn-add-site btn-flat" onclick="showAddSiteForm(this); return false;">
                <i class="fa fa-plus"></i> Add Site
            </button>
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
            <a href="#" class="btn-action" onclick="toggleEditProductName(this); return false;"
               data-toggle="tooltip" title="edit">
                <i class="fa fa-pencil-square-o"></i>
            </a>
            {!! Form::model($product, array('route' => array('product.destroy', $product->getKey()), 'method'=>'delete', 'class'=>'frm-delete-product', 'onsubmit' => 'return false;')) !!}
            <a href="#" class="btn-action" data-name="{{$product->product_name}}"
               onclick="btnDeleteProductOnClick(this); return false;"
               data-toggle="tooltip" title="delete">
                <i class="glyphicon glyphicon-trash text-danger"></i>
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
                        <th width="20%"></th>
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
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
    <script type="text/javascript">
        var siteDrake{{$product->getKey()}} = null;

        $(function(){
            siteDrake{{$product->getKey()}} = dragula([$("#product-{{$product->getKey()}} > table > tbody").get(0)], {
//                moves: function (el, container, handle) {
//                    return $(handle).hasClass("btn-product-dragger") || $(handle).closest(".btn-product-dragger").length > 0;
//                }
            }).on('drop', function (el, target, source, sibling) {
                updateSiteOrder({{$product->getKey()}});
            });
        });

        function assignSiteOrderNumber(product_id) {
            $(".product-wrapper").filter(function () {
                return $(this).attr("data-product-id") == product_id;
            }).find(".site-wrapper").each(function (index) {
                $(this).attr("data-order", index + 1);
            });
        }

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
                    alertP("Error", "Unable to update product order, please try again later.");
                }
            })
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
                                alertP("Error", "Unable to delete product, please try again later.");
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
                        var errorMsg = "Unable to add product. ";
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
                    alertP("Error", "Unable to update product, please try again later.");
                }
            });
        }


        function showAddSiteForm(el) {
            showLoading();
            var productID = $(el).closest(".product-wrapper").attr("data-product-id");
            $.ajax({
                "url": "{{route('site.create')}}",
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
                                "callback": function (response) {
                                    if (response.status == true) {
                                        showLoading();
                                        if (typeof response.site != 'undefined') {
                                            $.get(response.site.urls.show, function (html) {
                                                hideLoading();
                                                $(el).closest(".product-wrapper").find(".tbl-site tbody").append(html);
                                            });
                                        }
                                    } else {
                                        alertP("Unable to add site, please try again later.");
                                    }
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $("#modal-site-store").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to show add site form, please try again later.");
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
                    alertP("Error", "Unable to show add site form, please try again later.");
                }
            });
        }

        function showProductChart(url) {
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
                    alertP("Error", "Unable to show edit report form, please try again later.");
                }
            });
        }
    </script>
</table>