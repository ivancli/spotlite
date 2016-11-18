<table class="table table-hover table-condensed product-wrapper create">
    <thead>
    <tr>
        <th class="shrink hamburger-container">
            <a class="btn-collapse" href="#">
                <i class="glyphicon glyphicon-menu-hamburger"></i>
            </a>
        </th>
        <th>
            {!! Form::open(array('route' => array('product.store'), 'method'=>'post', 'class'=>'frm-add-product', 'onsubmit' => 'btnAddProductOnClick(this); return false;')) !!}
            <div class="input-group sl-input-group">
                <input type="hidden" name="category_id" value="{{$category->getKey()}}">
                <input type="text" name="product_name" class="form-control sl-form-control input-sm product-name"
                       placeholder="Product Name"
                       onkeyup="if(event.keyCode == 27){cancelCreateProduct(this)}">
                <span class="input-group-btn">
                            <button type="submit" class="btn btn-primary btn-flat btn-sm">Add</button>
                        </span>
            </div>
            {!! Form::close() !!}
        </th>
        <th class="cross-container text-right">
            <a href="#" class="btn-action" onclick="cancelCreateProduct(this)">
                <i class="fa fa-times"></i>
            </a>
        </th>
    </thead>
</table>


<script type="text/javascript">
    function cancelCreateProduct(el) {
        $(el).closest(".product-wrapper.create").remove();
    }

    function btnAddProductOnClick(el) {
        showLoading();
        $.ajax({
            "url": $(el).closest(".frm-add-product").attr("action"),
            "method": "post",
            "data": $(el).closest(".frm-add-product").serialize(),
            "dataType": "json",
            "success": function (response) {
                hideLoading();
                if (response.status == true) {
                    gaAddProduct();


                    if (response.product != null) {
                        showLoading();
                        loadSingleProduct(response.product.urls.show, function (html) {
                            hideLoading();
                            $(el).closest(".collapsible-category-div").prepend(
                                    html
                            );
                            updateProductOrder("{{$category->getKey()}}");
                            $(el).closest(".product-wrapper.create").remove();
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
            "error": function(xhr, status, error){
                hideLoading();
                describeServerRespondedError(xhr.status);
            }
        });
    }
</script>
