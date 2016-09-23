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
                        <i class="glyphicon glyphicon-menu-hamburger"></i>
                    </a>
                </th>
                <th class="category-th">
                    <a class="text-muted category-name-link" href="#category-{{$category->getKey()}}" role="button"
                       data-toggle="collapse" data-parent="#accordion" aria-expanded="true"
                       aria-controls="category-{{$category->getKey()}}">{{$category->category_name}}</a>
                    {!! Form::model($category, array('route' => array('category.update', $category->getKey()), 'method'=>'delete', 'class'=>'frm-edit-category', 'onsubmit' => 'submitEditCategoryName(this); return false;', 'style'=>'display: none;')) !!}
                    <div class="input-group sl-input-group">
                        <input type="text" name="category_name" placeholder="Category Name"
                               class="form-control sl-form-control input-sm category-name"
                               value="{{$category->category_name}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary btn-flat btn-sm">
                                <i class="fa fa-pencil"></i>
                            </button>
                        </span>
                    </div>
                    {!! Form::close() !!}
                    &nbsp;
                    <button class="btn btn-primary btn-xs" onclick="appendCreateProductBlock(this)">
                        <i class="fa fa-plus"></i> Add Product
                    </button>
                </th>

                <th class="text-right action-cell category-th">
                    <a href="#" class="btn-action"
                       onclick="showCategoryChart('{{$category->urls['chart']}}'); return false;">
                        <i class="fa fa-line-chart"></i>
                    </a>
                    <a href="#" class="btn-action">
                        <i class="fa fa-bell-o"></i>
                    </a>
                    <a href="#" class="btn-action" onclick="showCategoryReportTaskForm(this)">
                        <i class="fa {{!is_null($category->reportTask) ? "fa-envelope text-success" : "fa-envelope-o"}}"></i>
                    </a>
                    <a href="#" class="btn-action" onclick="toggleEditCategoryName(this)">
                        <i class="fa fa-pencil-square-o"></i>
                    </a>
                    {!! Form::model($category, array('route' => array('category.destroy', $category->getKey()), 'method'=>'delete', 'class'=>'frm-delete-category', 'onsubmit' => 'return false;')) !!}
                    <a href="#" class="btn-action" onclick="btnDeleteCategoryOnClick(this)">
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
                    <div id="category-{{$category->getKey()}}" class="collapse in collapsible-category-div"
                         aria-expanded="true">
                        @if($category->products->count() > 0)
                            @foreach($category->products()->orderBy('product_order')->orderBy('product_id')->get() as $product)
                                @include('products.product.partials.single_product')
                            @endforeach
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
            autoScroller([window], {
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
            confirmP("Delete Category", "Do you want to delete this category?", {
                "affirmative": {
                    "text": "Delete",
                    "class": "btn-danger",
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
                                    alertP("Delete Category", "Category has been deleted.");
                                    $(el).closest(".category-wrapper").remove();
                                } else {
                                    alertP("Error", "Unable to delete category, please try again later.");
                                }
                            },
                            "error": function (xhr, status, error) {
                                hideLoading();
                                alertP("Error", "Unable to delete category, please try again later.");
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

        function appendCreateProductBlock(el) {
            showLoading();
            var $categoryCollapsible = $(el).closest(".category-wrapper").find(".collapsible-category-div");
            if ($categoryCollapsible.attr("aria-expanded") == "false") {
                $categoryCollapsible.addClass("in").attr("aria-expanded", true);
            }
            var $div = $(el).closest(".tbl-category").find("tbody .collapsible-category-div");
            var categoryID = $(el).closest(".category-wrapper").attr("data-category-id");
            if ($div.find(".product-wrapper.create").length == 0) {
                $.ajax({
                    "url": "{{route('product.create')}}",
                    "method": "get",
                    "data": {
                        "category_id": categoryID
                    },
                    "success": function (html) {
                        hideLoading();
                        $div.prepend(html);
                        $div.find(".product-wrapper.create .product-name").focus();
                    }
                });
            } else {
                hideLoading();
                $div.find(".product-wrapper.create .product-name").focus();
            }
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
                        alertP("Update Category", "Category name has been updated.");
                        $(el).siblings(".category-name-link").text($(el).find(".category-name").val()).show();
                        $(el).hide();
                        $(el).closest(".tbl-category").find(".btn-action.editing").removeClass("editing");
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
                    alertP("Error", "Unable to update category, please try again later.");
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
                    }
                },
                "error": function (xhr, status, error) {
                    alertP("Error", "Unable to update product order, please try again later.");
                }
            })
        }

        function showCategoryChart(url) {
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
                    alertP("Error", "Unable to show edit report form, please try again later.");
                }
            });
        }
    </script>
</div>