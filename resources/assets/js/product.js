/* single site ******************************************************************************************************/

function btnDeleteSiteOnClick(el) {
    confirmP("Delete Site", "Do you want to delete this site?", {
        "affirmative": {
            "text": "Delete",
            "class": "btn-danger",
            "dismiss": true,
            "callback": function () {
                var $form = $(el).closest(".frm-delete-site");
                showLoading();
                $.ajax({
                    "url": $form.attr("action"),
                    "method": "delete",
                    "data": $form.serialize(),
                    "dataType": "json",
                    "success": function (response) {
                        hideLoading();
                        if (response.status == true) {
                            alertP("Delete Site", "The site has been deleted.");
                            $(el).closest(".site-wrapper").remove();
                        } else {
                            alertP("Error", "Unable to delete site, please try again later.");
                        }
                    },
                    "error": function (xhr, status, error) {
                        hideLoading();
                        alertP("Error", "Unable to delete site, please try again later.");
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

function btnEditSiteOnClick(el) {
    showLoading();
    $.ajax({
        "url": $(el).closest(".site-wrapper").attr("data-site-edit-url"),
        "method": "get",
        "data": {
            "product_site_id": $(el).closest(".site-wrapper").attr("data-product-site-id")
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
                        "callback": function (response) {
                            if (response.status == true) {
                                showLoading();
                                window.location.reload();
                            } else {
                                alertP("Unable to edit this site, please try again later.");
                            }
                        }
                    })
                }
            });
            $modal.on("hidden.bs.modal", function () {
                $("#modal-site-update").remove();
            });
        },
        "error": function () {
            hideLoading();
            alertP("Error", "Unable to edit this site, please try again later.");
        }
    })
}

/* single product ***************************************************************************************************/
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
        "url": "{{route('site.create')}}",
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
                        "callback": function (response) {
                            if (response.status == true) {
                                showLoading();
                                window.location.reload();
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

/* single category *************************************************************************************************/

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

/* create product ****************************************************************************************************/

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
            console.info(response);
            if (response.status == true) {
                if (response.product != null) {
                    showLoading();
                    loadSingleProduct(response.product.urls.show, function (html) {
                        hideLoading();
                        alertP("Create product", "product has been created.");
                        $(el).closest(".collapsible-category-div").append(
                            html
                        );
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
            alertP("Error", "Unable to add product, please try again later.");
        }
    })
}

function loadSingleProduct(url, callback) {
    $.get(url, callback);
}

/* create category ***************************************************************************************************/

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