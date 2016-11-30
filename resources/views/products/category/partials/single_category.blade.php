<div class="row category-wrapper" data-category-id="{{$category->getKey()}}" draggable="true"
     data-report-task-link="{{$category->urls['report_task']}}">
    <div class="col-sm-12">
        <table class="table table-condensed tbl-category">
            <thead>
            <tr>
                <th class="shrink category-th">
                    <a class="btn-collapse btn-category-dragger" href="#category-{{$category->getKey()}}" role="button"
                       data-toggle="collapse" data-parent="#accordion" aria-expanded="true"
                       aria-controls="category-{{$category->getKey()}}">
                        <i class="fa fa-bookmark "></i>
                    </a>
                </th>
                <th class="category-th">
                    <a class="text-muted category-name-link" href="#category-{{$category->getKey()}}" role="button"
                       data-toggle="collapse" data-parent="#accordion" aria-expanded="true"
                       aria-controls="category-{{$category->getKey()}}">{{$category->category_name}}</a>


                    {!! Form::model($category, array('route' => array('category.update', $category->getKey()), 'method'=>'delete', 'class'=>'frm-edit-category', 'onsubmit' => 'submitEditCategoryName(this); return false;', 'style' => 'display: none;')) !!}
                    <div class="input-group sl-input-group">
                        <input type="text" name="category_name" placeholder="Category Name"
                               class="form-control sl-form-control input-lg category-name"
                               value="{{$category->category_name}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default btn-flat btn-lg">
                                <i class="fa fa-pencil"></i>
                            </button>
                        </span>
                    </div>
                    {!! Form::close() !!}

                    <span class="btn-edit btn-edit-category" onclick="toggleEditCategoryName(this)">Edit &nbsp; <i
                                class="fa fa-pencil-square-o"></i></span>
                </th>

                <th class="text-right action-cell category-th">
                    <a href="#" class="btn-action btn-chart" data-toggle="tooltip" title="chart"
                       onclick="showCategoryChart('{{$category->urls['chart']}}'); return false;">
                        <i class="fa fa-line-chart"></i>
                    </a>
                    <a href="#" class="btn-action btn-report" onclick="showCategoryReportTaskForm(this); return false;"
                       data-toggle="tooltip"
                       title="report">
                        <i class="fa {{!is_null($category->reportTask) ? "fa-envelope text-success" : "fa-envelope-o"}}"></i>
                    </a>
                    {!! Form::model($category, array('route' => array('category.destroy', $category->getKey()), 'method'=>'delete', 'class'=>'frm-delete-category', 'onsubmit' => 'return false;')) !!}
                    <a href="#" data-name="{{$category->category_name}}" class="btn-action"
                       onclick="btnDeleteCategoryOnClick(this); return false;" data-toggle="tooltip"
                       title="delete">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                    {!! Form::close() !!}
                </th>
            </tr>
            <tr>
                <th></th>
                <th colspan="2" class="category-th action-cell">
                    <div class="add-item-block add-product-container"
                         onclick="appendCreateProductBlock(this); event.stopPropagation(); return false;">
                        <div class="add-item-label">
                            <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;
                            <span class="add-item-text">ADD PRODUCT</span>
                        </div>
                        <div class="add-item-controls">
                            <div class="row">
                                <div class="col-lg-8 col-md-7 col-sm-5 col-xs-4">
                                    <form action="{{route('product.store')}}" method="post"
                                          class="frm-store-product"
                                          onsubmit="btnAddProductOnClick(this); return false;">
                                        <input type="text" placeholder="Product Name" name="product_name"
                                               id="txt-product-name-{{$category->getKey()}}"
                                               class="form-control txt-item">
                                    </form>
                                </div>
                                <div class="col-lg-4 col-md-5 col-sm-7 col-xs-8 text-right">
                                    <button class="btn btn-primary"
                                            onclick="btnAddProductOnClick(this); event.stopPropagation(); event.preventDefault();">
                                        ADD PRODUCT
                                    </button>
                                    &nbsp;&nbsp;
                                    <button class="btn btn-default" id="btn-cancel-add-product-{{$category->getKey()}}"
                                            onclick="cancelAddProduct(this); event.stopPropagation(); event.preventDefault();">
                                        CANCEL
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td colspan="2" class="table-container">
                    <div id="category-{{$category->getKey()}}" class="collapse in collapsible-category-div"
                         aria-expanded="true">
                        @if($category->products->count() > 0)
                            @if(request()->has('keyword') && !empty(request()->get('keyword')) && strpos(strtolower($category->category_name), strtolower(request()->get('keyword'))) === FALSE)
                                @foreach($category->filteredProducts()->orderBy('product_order')->orderBy('product_id')->get() as $product)
                                    @include('products.product.partials.single_product')
                                @endforeach
                            @else
                                @foreach($category->products()->orderBy('product_order')->orderBy('product_id')->get() as $product)
                                    @include('products.product.partials.single_product')
                                @endforeach
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <script type="text/javascript">

        var productDrake{{$category->getKey()}} = null;

        $(function () {

            productDrake{{$category->getKey()}} = dragula([$("#category-{{$category->getKey()}}").get(0)], {
                moves: function (el, container, handle) {
                    return $(handle).hasClass("btn-product-dragger") || $(handle).closest(".btn-product-dragger").length > 0;
                }
            }).on('drop', function (el, target, source, sibling) {
                updateProductOrder({{$category->getKey()}});
            });


            /** enable scrolling when dragging*/
            autoScroll([window], {
                margin: 20,
                pixels: 20,
                scrollWhenOutside: true,
                autoScroll: function () {
                    //Only scroll when the pointer is down, and there is a child being dragged.
                    return this.down && productDrake{{$category->getKey()}}.dragging;
                }
            });
        });

        function btnDeleteCategoryOnClick(el) {
            confirmP("Delete Category", "Are you sure you want to delete the " + $(el).attr("data-name") + " Category?", {
                "affirmative": {
                    "text": "Delete",
                    "class": "btn-danger btn-flat",
                    "dismiss": true,
                    "callback": function () {
                        var $form = $(el).closest(".frm-delete-category");
                        showLoading();
                        $.ajax({
                            "url": $form.attr("action"),
                            "method": "delete",
                            "data": $form.serialize(),
                            "dataType": "json",
                            "success": function (response) {
                                hideLoading();
                                if (response.status == true) {
                                    gaDeleteCategory();
                                    alertP("Delete Category", "Category has been deleted.");
                                    $(el).closest(".category-wrapper").remove();
                                } else {
                                    alertP("Error", "Unable to delete category, please try again later.");
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

        function appendCreateProductBlock(el) {
            $(el).find(".add-item-label").slideUp();
            $(el).find(".add-item-controls").slideDown();
            $("#txt-product-name-{{$category->getKey()}}").focus();
        }

        function cancelAddProduct(el) {
            $(el).closest(".add-item-block").find(".add-item-label").slideDown();
            $(el).closest(".add-item-block").find(".add-item-controls").slideUp();
            $(el).closest(".add-item-block").find(".add-item-controls input").val("");
        }


        function btnAddProductOnClick(el) {
            showLoading();
            $.ajax({
                "url": "{{route('product.store')}}",
                "method": "post",
                "data": {
                    "category_id": "{{$category->getKey()}}",
                    "product_name": $("#txt-product-name-{{$category->getKey()}}").val()
                },
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        cancelAddProduct($("#btn-cancel-add-product-{{$category->getKey()}}").get());
                        gaAddProduct();
                        if (response.product != null) {
                            showLoading();
                            loadSingleProduct(response.product.urls.show, function (html) {
                                hideLoading();
                                $(el).closest(".tbl-category").find(".collapsible-category-div").prepend(html);
                                updateProductOrder("{{$category->getKey()}}");
                                updateProductEmptyMessage();
                            });
                        } else {
                            alertP("Create product", "product has been created. But encountered error while page being loaded.", function () {
                                window.location.reload();
                            });
                        }
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
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function loadSingleProduct(url, callback) {
            $.ajax({
                "url": url,
                "method": "get",
                "success": callback,
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }


        function toggleEditCategoryName(el) {
            var $tbl = $(el).closest(".tbl-category");
            if ($(el).hasClass("editing")) {
                $(el).removeClass("editing");
                $tbl.find(".category-name-link").show();
                $tbl.find(".frm-edit-category").hide();
            } else {
                $tbl.find(".category-name-link").hide();
                $tbl.find(".frm-edit-category").show();
                $tbl.find(".frm-edit-category .category-name").focus();
                $(el).addClass("editing");
            }
        }

        function submitEditCategoryName(el) {
            showLoading();
            $.ajax({
                "url": $(el).attr("action"),
                "method": "put",
                "data": $(el).serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        gaEditCategory();
                        alertP("Update Category", "Category name has been updated.");
                        $(el).siblings(".category-name-link").text($(el).find(".category-name").val()).show();
                        $(el).hide();
                        $(el).closest(".tbl-category").find(".btn-action.editing").removeClass("editing");
                    } else {
                        var errorMsg = "Unable to edit category name. ";
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

        function assignProductOrderNumber(category_id) {
            $(".category-wrapper").filter(function () {
                return $(this).attr("data-category-id") == category_id;
            }).find(".product-wrapper").each(function (index) {
                $(this).attr("data-order", index + 1);
            });
        }

        function updateProductOrder(category_id) {
            assignProductOrderNumber(category_id);
            var orderList = [];
            $(".category-wrapper").filter(function () {
                return $(this).attr("data-category-id") == category_id;
            }).find(".product-wrapper").filter(function () {
                return !$(this).hasClass("gu-mirror");
            }).each(function () {
                if ($(this).attr("data-product-id")) {
                    var productId = $(this).attr("data-product-id");
                    var productOrder = parseInt($(this).attr("data-order"));
                    orderList.push({
                        "product_id": productId,
                        "product_order": productOrder
                    });
                }
            });
            $.ajax({
                "url": "{{route('product.order')}}",
                "method": "put",
                "data": {
                    "order": orderList
                },
                "dataType": "json",
                "success": function (response) {
                    if (response.status == false) {
                        alertP("Error", "Unable to update product order, please try again later.");
                    } else {
                        gaMoveProduct();
                    }
                },
                "error": function (xhr, status, error) {
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function showCategoryChart(url) {
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


        function showCategoryReportTaskForm(el) {
            showLoading();
            $.ajax({
                "url": $(el).closest(".category-wrapper").attr("data-report-task-link"),
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
                        $("#modal-report-task-category").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }
    </script>
</div>