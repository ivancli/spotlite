<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-sm-12">
                <p>Please download <a href="{{asset('build/csvs/import_products_template.csv')}}">import products template</a> in order to import in correct format.</p>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-offset-3 col-sm-9">
                        <ul class="text-danger errors-container">
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-offset-3 col-sm-9">
                        <ul class="text-danger warnings-container">
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-offset-3 col-sm-9">
                        <ul class="text-success success-msg-container">
                        </ul>
                    </div>
                </div>
                <form action="{{route('product_import.product.store')}}" method="post" enctype="multipart/form-data" class="form-horizontal sl-form-horizontal" onsubmit="submitProductImport(this); return false">
                    {!! csrf_field() !!}
                    {{--TODO options here--}}
                    <input type="hidden" name="import_type" value="product">

                    <div class="form-group">
                        <label for="file" class="col-sm-3 control-label">Select CSV File</label>
                        <div class="col-sm-9">
                            <input type="file" name="file" id="file" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <div class="checkbox">
                                <label for="chk-no-new-categories">
                                    <input type="checkbox" name="no_new_categories" id="chk-no-new-categories">
                                    Do not create new categories
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <div class="checkbox">
                                <label for="chk-no-new-products">
                                    <input type="checkbox" name="no_new_products" id="chk-no-new-products">
                                    Do not create new products
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <div class="checkbox">
                                <label for="chk-no-product-meta-update">
                                    <input type="checkbox" name="no_product_meta_update" id="chk-no-product-meta-update">
                                    Do not update existing products' meta data
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary btn-flat">IMPORT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function submitProductImport(el) {
        $(".success-msg-container").empty();
        $(".warning-msg-container").empty();
        $(".errors-container").empty();
        var $form = $(el);
        $.ajax({
            'url': $form.attr('action'),
            'method': $form.attr('method'),
            'data': (new FormData(el)),
            'cache': false,
            'contentType': false,
            'processData': false,
            'success': function (response) {
                if (response.status == true) {
                    $(".success-msg-container").append(
                            $("<li>").text("Data has been imported to your account.")
                    ).append(function () {
                        if (response.categoryCounter > 0) {
                            return $("<div>").append(
                                    $("<li>").text("Imported " + response.categoryCounter + ' new categories.')
                            ).html();
                        } else {
                            return '';
                        }
                    }).append(function () {
                        if (response.productCounter > 0) {
                            return $("<div>").append(
                                    $("<li>").text("Imported " + response.productCounter + ' new products.')
                            ).html();
                        } else {
                            return '';
                        }
                    }).append(function () {
                        if (response.siteCounter > 0) {
                            return $("<div>").append(
                                    $("<li>").text("Imported " + response.siteCounter + ' new sites.')
                            ).html();
                        } else {
                            return '';
                        }
                    });

                    $.each(response.warnings, function (index, warning) {
                        $(".warnings-container").append(
                                $("<li>").text(warning)
                        )
                    });

                }
            },
            'error': function (xhr, status, error) {
                if (xhr.status == 422) {
                    var $errorContainer = $(".errors-container");
                    $.each(xhr.responseJSON, function (index, error) {
                        $.each(error, function (index, message) {
                            $errorContainer.append(
                                    $("<li>").text(message)
                            );
                        })
                    });
                } else {
                    describeServerRespondedError(xhr.status);
                }
            }
        })
    }
</script>
