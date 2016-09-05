<div class="row category-wrapper create">
    <div class="col-sm-7">
        <table class="table table-condensed tbl-category">
            <thead>
            <tr>
                <th class="shrink hamburger-container">
                    <a class="btn-collapse" href="#">
                        <i class="glyphicon glyphicon-menu-hamburger"></i>
                    </a>
                </th>
                <th>

                    {!! Form::open(array('route' => array('category.store'), 'method'=>'post', 'class'=>'frm-add-category', 'onsubmit' => 'btnAddCategoryOnClick(this); return false;')) !!}
                    <div class="input-group sl-input-group">
                        <input type="text" name="category_name" class="form-control sl-form-control input-sm"
                               placeholder="Category Name">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary btn-flat btn-sm">Add</button>
                        </span>
                    </div>
                    {!! Form::close() !!}
                    {{--<input type="text" class="form-control sl-form-control input-sm" placeholder="Category Name">--}}
                </th>
                <th class="cross-container text-right">
                    <a href="#" class="btn-action" onclick="cancelCreateCategory(this)">
                        <i class="fa fa-times"></i>
                    </a>
                </th>
            </tr>
            </thead>
        </table>
    </div>
    <script type="text/javascript">
        function cancelCreateCategory(el) {
            $(el).closest(".category-wrapper.create").remove();
        }

        function btnAddCategoryOnClick(el) {
            showLoading();
            $.ajax({
                "url": $(el).closest(".frm-add-category").attr("action"),
                "method": "post",
                "data": $(el).closest(".frm-add-category").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    console.info(response);
                    if (response.status == true) {
                        alertP("Create Category", "Category has been created.");
                        window.location.reload();
                    } else {
                        alertP("Error", "Unable to add category, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to add category, please try again later.");
                }
            })
        }
    </script>
</div>