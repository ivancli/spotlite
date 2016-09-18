<table class="table table-condensed product-wrapper" data-product-id="{{$product->getKey()}}"
       data-alert-link="{{$product->urls['alert']}}">
    <thead>
    <tr>
        <th class="shrink product-th">
            <a class="btn-collapse btn-product-dragger" href="#product-{{$product->getKey()}}" role="button" data-toggle="collapse"
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
            <button class="btn btn-primary btn-xs" onclick="showAddSiteForm(this); return false;">
                <i class="fa fa-plus"></i> Add Site
            </button>
        </th>
        <th class="text-right action-cell product-th">
            <a href="#" class="btn-action">
                <i class="fa fa-line-chart"></i>
            </a>
            <a href="#" class="btn-action" onclick="showProductAlertForm(this); return false;">
                <i class="fa {{!is_null($product->alert) ? "fa-bell alert-enabled" : "fa-bell-o"}}"></i>
            </a>
            <a href="#" class="btn-action">
                <i class="fa fa-envelope-o"></i>
            </a>
            <a href="#" class="btn-action" onclick="toggleEditProductName(this); return false;">
                <i class="fa fa-pencil-square-o"></i>
            </a>
            {!! Form::model($product, array('route' => array('product.destroy', $product->getKey()), 'method'=>'delete', 'class'=>'frm-delete-product', 'onsubmit' => 'return false;')) !!}
            <a href="#" class="btn-action" onclick="btnDeleteProductOnClick(this); return false;">
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
                        <th>Site</th>
                        <th>Price</th>
                        <th>My Price</th>
                        <th>Last Update</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--sites here--}}
                    @if(!is_null($product->sites))
                        @foreach($product->productSites()->orderBy('my_price', 'asc')->orderBy('product_site_id')->get() as $productSite)
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

        function btnDeleteProductOnClick(el) {
            confirmP("Delete Product", "Do you want to delete this product?", {
                "affirmative": {
                    "text": "Delete",
                    "class": "btn-danger",
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
                    "class": "btn-default",
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
                "url": "{{route('product_site.create')}}",
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
                                        if (typeof response.productSite != 'undefined') {
                                            $.get(response.productSite.urls.show, function (html) {
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
                        $("#modal-alert-product").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to show add site form, please try again later.");
                }
            });
        }
    </script>
</table>