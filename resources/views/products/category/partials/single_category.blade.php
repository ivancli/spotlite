<div class="row category-wrapper">
    <div class="col-sm-7">
        <table class="table table-condensed tbl-category">
            <thead>
            <tr>
                <th class="shrink">
                    <a class="btn-collapse" href="#category-{{$category->getKey()}}" role="button"
                       data-toggle="collapse" data-parent="#accordion" aria-expanded="true"
                       aria-controls="category-{{$category->getKey()}}">
                        <i class="glyphicon glyphicon-menu-hamburger"></i>
                    </a>
                </th>
                <th>
                    {{$category->category_name}}
                    &nbsp;
                    <button class="btn btn-primary btn-xs">
                        <i class="fa fa-plus"></i> Add Product
                    </button>
                </th>

                <th class="text-right action-cell">
                    <a href="#" class="btn-action">
                        <i class="glyphicon glyphicon-envelope"></i>
                    </a>
                    <a href="#" class="btn-action">
                        <i class="glyphicon glyphicon-cog"></i>
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
                    <div id="category-{{$category->getKey()}}" class="collapse in" aria-expanded="true">
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
                                    window.location.reload();
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
    </script>
</div>