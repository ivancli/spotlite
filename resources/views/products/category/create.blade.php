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
                        <input type="text" name="category_name" class="form-control sl-form-control input-sm category-name"
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
            $(el).closest(".category-wrapper.create").slideUp("fast", function () {
                $(this).remove();
            });
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
                        if (response.category != null) {
                            showLoading();
                            loadSingleCategory(response.category.urls.show, function (html) {
                                hideLoading();
                                alertP("Create Category", "Category has been created.");
                                $(el).closest(".list-container").append(
                                        html
                                );
                                $(el).closest(".category-wrapper.create").remove();
                            });
                        } else {
                            alertP("Create Category", "Category has been created. But encountered error while page being lodaed.", function () {
                                window.location.reload();
                            });
                        }
                    } else {

                        var errorMsg = "Unable to add category. ";
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
                    alertP("Error", "Unable to add category, please try again later.");
                }
            })
        }

        function loadSingleCategory(url, callback) {
            $.get(url, callback);
        }
    </script>
</div>