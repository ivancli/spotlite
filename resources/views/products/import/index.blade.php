<div class="row">
    <div class="col-md-offset-2 col-md-8">
        <div class="row m-b-20">
            <div class="col-sm-12">
                In order to Bulk Import data into SpotLite, please download the
                <a href="{{asset('build/csvs/import_categories_products_template.csv')}}" download>Bulk Import Categories And Products Template</a>
                and
                <a href="{{asset('build/csvs/import_urls_template.csv')}}" download>Bulk Import Product Page URLs Template</a>.
            </div>
        </div>

        <div class="row m-b-20">
            <div class="col-sm-12">
                <p><strong>Instructions:</strong></p>
                <p>If you wish to import Categories, Products and Product Page URLs, please go to <span class="text-green">STEP 1</span>.</p>
                <p>If you wish to import Product Page URLs only, please go to <span class="text-green">STEP 2</span>.</p>
            </div>
        </div>

        <div class="row m-b-20">
            <div class="col-sm-12">
                <p><strong class="text-danger">Important:</strong></p>
                <p>The following actions might cause errors:</p>
                <ul>
                    <li>Do not remove any columns (all Categories and Products are mandatory)</li>
                    <li>Do not leave Category or Product blank (each product must belong to a Category)</li>
                    <li>Errors or misspellings on Category or Product names will result in the creation of new Category or Product</li>
                    <li>There are 2 templates - first one for Categories and Products and second one for Product Page URLs. Make sure you save each template as a CSV file before uploading it on step 1
                        and 2 respectively.
                    </li>
                </ul>
            </div>
        </div>

        <div class="row m-b-20 import-product-container">
            <div class="col-sm-12">
                <h4 class="text-green">STEP 1 - IMPORT CATEGORIES AND PRODUCTS</h4>
                <p>Choose the Bulk Import CSV file you wish to upload into SpotLite</p>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="text-danger errors-container">
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="text-danger warnings-container">
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="text-success success-msg-container">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{route('product_import.product.store')}}" method="post" enctype="multipart/form-data" class="form-horizontal sl-form-horizontal"
                      onsubmit="submitProductImport(this); return false">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="file" class="col-sm-3 control-label">Select CSV File</label>
                        <div class="col-sm-9">
                            <input type="file" name="file" id="file" class="form-control">
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary btn-flat">CONFIRM</button>
                    </div>
                </form>
            </div>
        </div>

        <hr class="m-b-20">

        <style>
            .opacity-03 {
                opacity: 0.3;
            }
        </style>

        <div class="row m-b-20 import-site-container {{auth()->user()->products()->count() == 0 ? "opacity-03" : ""}}">

            <div class="col-sm-12">
                @if(auth()->user()->products()->count() == 0)
                    <div class="import-site-blocker" style="position: absolute; top: 0; right: 0; left: 0; bottom: 0; z-index: 99999;"></div>
                @endif
                <h4 class="text-green">STEP 2 - IMPORT PRODUCT PAGE URLs</h4>
                <p>Since you already have Categories and Products set up, you may not wish to have new Categories or Products created or existing Product meta Data replaced by your Bulk Import data.
                    If that's the case, please choose one or more options from the following:</p>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="text-danger errors-container">
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="text-danger warnings-container">
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <ul class="text-success success-msg-container">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{route('product_import.site.store')}}" method="post" enctype="multipart/form-data" class="form-horizontal sl-form-horizontal"
                      onsubmit="submitURLImport(this); return false">
                    {!! csrf_field() !!}
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
                        <label for="file" class="col-sm-3 control-label">Select CSV File</label>
                        <div class="col-sm-9">
                            <input type="file" name="file" id="file" class="form-control">
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary btn-flat">CONFIRM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function submitProductImport(el) {
        $(".success-msg-container").empty();
        $(".warnings-container").empty();
        $(".errors-container").empty();
        var $form = $(el);
        showLoading();
        $.ajax({
            'url': $form.attr('action'),
            'method': $form.attr('method'),
            'data': (new FormData(el)),
            'cache': false,
            'contentType': false,
            'processData': false,
            'success': function (response) {
                hideLoading();
                if (response.status == true) {
                    $(".import-product-container .success-msg-container").append(
                        $("<li>").text("Data has been imported to your account.")
                    ).append(function () {
                        if (response.categoryCounter >= 0) {
                            return $("<div>").append(
                                $("<li>").text("Imported " + response.categoryCounter + ' new categories.')
                            ).html();
                        } else {
                            return '';
                        }
                    }).append(function () {
                        if (response.productCounter >= 0) {
                            return $("<div>").append(
                                $("<li>").text("Imported " + response.productCounter + ' new products.')
                            ).html();
                        } else {
                            return '';
                        }
                    });

                    if (response.productCounter > 0) {
                        $(".opacity-03").removeClass("opacity-03");
                        $(".import-site-blocker").remove();
                    }

                    $.each(response.warnings, function (index, warning) {
                        $(".import-product-container .warnings-container").append(
                            $("<li>").text(warning)
                        )
                    });

                }
            },
            'error': function (xhr, status, error) {
                hideLoading();
                if (xhr.status == 422) {
                    var $errorContainer = $(".import-product-container .errors-container");
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

    function submitURLImport(el) {
        $(".success-msg-container").empty();
        $(".warning-msg-container").empty();
        $(".errors-container").empty();
        var $form = $(el);
        showLoading();
        $.ajax({
            'url': $form.attr('action'),
            'method': $form.attr('method'),
            'data': (new FormData(el)),
            'cache': false,
            'contentType': false,
            'processData': false,
            'success': function (response) {
                hideLoading();
                if (response.status == true) {
                    $(".import-site-container .success-msg-container").append(
                        $("<li>").text("Data has been imported to your account.")
                    ).append(function () {
                        if (response.categoryCounter >= 0) {
                            return $("<div>").append(
                                $("<li>").text("Imported " + response.categoryCounter + ' new categories.')
                            ).html();
                        } else {
                            return '';
                        }
                    }).append(function () {
                        if (response.productCounter >= 0) {
                            return $("<div>").append(
                                $("<li>").text("Imported " + response.productCounter + ' new products.')
                            ).html();
                        } else {
                            return '';
                        }
                    }).append(function () {
                        if (response.siteCounter >= 0) {
                            return $("<div>").append(
                                $("<li>").text("Imported " + response.siteCounter + ' new sites.')
                            ).html();
                        } else {
                            return '';
                        }
                    });

                    $.each(response.warnings, function (index, warning) {
                        $(".import-site-container .warnings-container").append(
                            $("<li>").text(warning)
                        )
                    });

                }
            },
            'error': function (xhr, status, error) {
                hideLoading();
                if (xhr.status == 422) {
                    var $errorContainer = $(".import-site-container .errors-container");
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
