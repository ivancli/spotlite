<div class="row category-wrapper" data-category-id="{{$category->getKey()}}">
    <div class="col-sm-7">
        <table class="table table-condensed tbl-category">
            <thead>
            <tr>
                <th class="shrink category-th">
                    <a class="btn-collapse" href="#category-{{$category->getKey()}}" role="button"
                       data-toggle="collapse" data-parent="#accordion" aria-expanded="true"
                       aria-controls="category-{{$category->getKey()}}">
                        <i class="glyphicon glyphicon-menu-hamburger"></i>
                    </a>
                </th>
                <th class="category-th">
                    <a class="text-muted category-name-link" href="#category-{{$category->getKey()}}" role="button"
                       data-toggle="collapse"
                       data-parent="#accordion" aria-expanded="true"
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
                    <a href="#" class="btn-action">
                        <i class="fa fa-line-chart"></i>
                    </a>
                    <a href="#" class="btn-action">
                        <i class="fa fa-bell-o"></i>
                    </a>
                    <a href="#" class="btn-action">
                        <i class="fa fa-envelope-o"></i>
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
                        @if(!is_null($category->products))
                            @foreach($category->products as $product)
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
                        $div.append(html);
                        $div.find(".product-wrapper.create .product-name").focus();
                    }
                });
            } else {
                hideLoading();
                $div.find(".product-wrapper.create .product-name").focus();
            }
        }

        function toggleEditCategoryName(el) {
            var $tbl = $(el).closest(".tbl-category")
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

    </script>
</div>