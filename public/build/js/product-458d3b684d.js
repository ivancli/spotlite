function btnDeleteCategoryOnClick(el) {
    deletePopup("Delete Category", "Are you sure you want to delete this Category?",
        "By deleting this category, you will lose the following:",
        [
            "All Products you have added",
            "All URLs you have added",
            "All Category and Product Charts generated, including any Charts displayed on your Dashboards",
            "All Category Reports generated",
            "This Category's pricing information tracked to date"
        ],
        {
            "affirmative": {
                "text": "DELETE",
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
                                updateUserProductCredit();
                            } else {
                                if (typeof response.errors != 'undefined') {
                                    var errorMessage = "";
                                    $.each(response.errors, function (index, error) {
                                        errorMessage += error + " ";
                                    });
                                    alertP("Oops! Something went wrong.", errorMessage);
                                } else {
                                    alertP("Oops! Something went wrong.", "Unable to delete category, please try again later.");
                                }
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
                "text": "CANCEL",
                "class": "btn-default btn-flat",
                "dismiss": true
            }
        });
}

function appendCreateProductBlock(el) {
    $(el).find(".add-item-label").slideUp();
    $(el).find(".add-item-controls").slideDown();
    $(el).find(".txt-product-name").focus();
}

function appendUpgradeForCreateProductBlock(el) {
    $(el).find(".add-item-label").slideUp();
    $(el).find(".upgrade-for-add-item-controls").slideDown();
}

function cancelAddProduct(el) {
    $(el).closest(".add-item-block").find(".add-item-label").slideDown();
    $(el).closest(".add-item-block").find(".add-item-controls").slideUp();
    $(el).closest(".add-item-block").find(".add-item-controls input").val("");
}


function btnAddProductOnClick(el) {
    showLoading();
    $.ajax({
        "url": "/product",
        "method": "post",
        "data": {
            "category_id": $(el).closest(".category-wrapper").attr('data-category-id'),
            "product_name": $(el).closest(".category-wrapper").find(".txt-product-name").val()
        },
        "dataType": "json",
        "success": function (response) {
            hideLoading();
            if (response.status == true) {
                cancelAddProduct($(el).closest(".category-wrapper").find(".btn-cancel-add-product"));
                gaAddProduct();
                if (response.product != null) {
                    showLoading();
                    loadSingleProduct(response.product.urls.show, function (html) {
                        hideLoading();
                        $(el).closest(".tbl-category").find(".collapsible-category-div").prepend(html);
                        updateProductOrder($(el).closest(".category-wrapper").attr('data-category-id'));
                        updateProductEmptyMessage();
                        updateUserProductCredit();
                    });
                } else {
                    alertP("Create product", "product has been created. But encountered error while page being loaded.", function () {
                        window.location.reload();
                    });
                }
            } else {
                alertP("Oops! Something went wrong.", 'Unable to add product, please try again later.');
            }
        },
        "error": function (xhr, status, error) {
            hideLoading();
            if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                var errorMsg = "";
                $.each(xhr.responseJSON, function (key, error) {
                    $.each(error, function (index, message) {
                        errorMsg += message + " ";
                    })
                });
                alertP("Oops! Something went wrong.", errorMsg);
            } else {
                describeServerRespondedError(xhr.status);
            }
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
    if ($tbl.find(".btn-edit-category").hasClass("editing")) {
        $tbl.find(".btn-edit-category").removeClass("editing").show();
        $tbl.find(".category-name-link").show();
        $tbl.find(".frm-edit-category").hide();
    } else {
        $tbl.find(".btn-edit-category").addClass("editing").hide();
        $tbl.find("input.category-name").val($tbl.find(".category-name-link").text());
        $tbl.find(".category-name-link").hide();
        $tbl.find(".frm-edit-category").show();
        $tbl.find(".frm-edit-category .category-name").focus();
    }
}

function cancelEditCategoryName(el, event) {
    if (typeof event == 'undefined' || typeof event.keyCode == 'undefined') {
        toggleEditCategoryName(el);
    } else if (event.keyCode == 27) {
        $(el).blur();
    }
}

function txtCategoryOnBlur(el) {
    setTimeout(function () {
        if (!$(":focus").is($(el).siblings("span").find("button"))) {
            cancelEditCategoryName(el);
        }
    }, 10);
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
                $(el).closest(".tbl-category").find(".btn-edit-category.editing").removeClass("editing").show();
            } else {
                alertP("Oops! Something went wrong.", 'Unable to update category name, please try again later.');
            }
        },
        "error": function (xhr, status, error) {
            hideLoading();
            if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                var errorMsg = "";
                $.each(xhr.responseJSON, function (key, error) {
                    $.each(error, function (index, message) {
                        errorMsg += message + " ";
                    })
                });
                alertP("Oops! Something went wrong.", errorMsg);
            } else {
                describeServerRespondedError(xhr.status);
            }
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
        "url": "product/order",
        "method": "put",
        "data": {
            "order": orderList
        },
        "dataType": "json",
        "success": function (response) {
            if (response.status == false) {
                if (typeof response.errors != 'undefined') {
                    var errorMessage = "";
                    $.each(response.errors, function (index, error) {
                        errorMessage += error + " ";
                    });
                    alertP("Oops! Something went wrong.", errorMessage);
                } else {
                    alertP("Oops! Something went wrong.", "Unable to update product order, please try again later.");
                }
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
                                $(el).find("i").removeClass().addClass("fa fa-envelope ico-report-enabled");
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


function updateUserSiteUsage(el) {
    $.ajax({
        "url": $(el).closest(".category-wrapper").attr("data-get-site-usage-link"),
        "method": "get",
        "dataType": "json",
        "success": function (response) {
            if (response.status == true) {
                var usage = response.usage;
                var $categoryWrapper = $(el).closest(".category-wrapper")
                $categoryWrapper.find(".lbl-site-usage").text(usage);
            }
        },
        "error": function (xhr, status, error) {
            describeServerRespondedError(xhr.status);
        }
    })
}
/**
 * set order number to element
 * @param product_id
 */
function assignSiteOrderNumber(product_id) {
    $(".product-wrapper").filter(function () {
        return $(this).attr("data-product-id") == product_id;
    }).find(".site-wrapper").each(function (index) {
        $(this).attr("data-order", index + 1);
    });
}

/**
 * Send order number to server
 * @param product_id
 */
function updateSiteOrder(product_id) {
    assignSiteOrderNumber(product_id);
    var orderList = [];
    $(".product-wrapper").filter(function () {
        return $(this).attr("data-product-id") == product_id;
    }).find(".site-wrapper").filter(function () {
        return !$(this).hasClass("gu-mirror");
    }).each(function () {
        if ($(this).attr("data-site-id")) {
            var siteId = $(this).attr("data-site-id");
            var siteOrder = parseInt($(this).attr("data-order"));
            orderList.push({
                "site_id": siteId,
                "site_order": siteOrder
            });
        }
    });
    $.ajax({
        "url": "/site/order",
        "method": "put",
        "data": {
            "order": orderList
        },
        "dataType": "json",
        "success": function (response) {
            if (response.status == false) {
                if (typeof response.errors != 'undefined') {
                    var errorMessage = "";
                    $.each(response.errors, function (index, error) {
                        errorMessage += error + " ";
                    });
                    alertP("Oops! Something went wrong.", errorMessage);
                } else {
                    alertP("Oops! Something went wrong.", "Unable to update site order, please try again later.");
                }
            } else {
                gaMoveSite();
            }
        },
        "error": function (xhr, status, error) {
            describeServerRespondedError(xhr.status);
        }
    })
}
function appendCreateSiteBlock(el) {
    $(el).find(".add-item-label").slideUp();
    $(el).find(".add-item-controls").slideDown();
    $(el).find(".txt-site-url").focus();
}
function appendUpgradeForCreateSiteBlock(el) {
    $(el).find(".add-item-label").slideUp();
    $(el).find(".upgrade-for-add-item-controls").slideDown();
}
/**
 * disable add site
 * @param el
 */
function cancelAddSite(el) {
    $(el).closest(".add-item-block").find(".add-item-label").slideDown();
    $(el).closest(".add-item-block").find(".add-item-controls").slideUp();
    $(el).closest(".add-item-block").find(".add-item-controls input").val("");
}
function getPricesCreate(el) {
    var $addItemControls = $(el).closest(".add-item-controls");
    var $txtSiteURL = $addItemControls.find(".txt-site-url");
    var productID = $(el).closest(".product-wrapper").attr("data-product-id");
    showLoading();
    $.ajax({
        "url": "/site/prices",
        "method": "post",
        "data": {
            "site_url": $txtSiteURL.val()
        },
        "dataType": "json",
        "success": function (response) {
            if (typeof response.errors == 'undefined') {
                if ((typeof response.sites == 'undefined' || response.sites.length == 0) && typeof response.targetDomain == 'undefined') {
                    addSite({
                        "site_url": $txtSiteURL.val(),
                        "product_id": productID
                    }, function (add_site_response) {
                        alertP("Added Product Page URL", "This price will be updated soon. Note: If it doesn't come up in up to 48 hours, please contact us.");
                        if (add_site_response.status == true) {
                            loadSingleSite(add_site_response.site.urls.show, function (html) {
                                $(el).closest(".tbl-site").find("tbody").prepend(html);
                                cancelAddSite($addItemControls.find(".btn-cancel-add-site").get(0));
                                updateProductEmptyMessage();
                                updateUserSiteUsage(el);
                                updateUserSiteUsagePerProduct(el);
                            });
                        } else {
                            if (typeof response.errors != 'undefined') {
                                var errorMessage = "";
                                $.each(response.errors, function (index, error) {
                                    errorMessage += error + " ";
                                });
                                alertP("Oops! Something went wrong.", errorMessage);
                            } else {
                                alertP("Oops! Something went wrong.", "Unable to add site, please try again later.");
                            }
                        }
                    })
                } else {
                    showLoading();
                    $.ajax({
                        "url": "/site/prices",
                        "method": "post",
                        "data": {
                            "site_url": $txtSiteURL.val()
                        },
                        "success": function (html) {
                            hideLoading();
                            var $modal = $(html);
                            $modal.modal();
                            $modal.on("shown.bs.modal", function () {
                                if ($.isFunction(modalReady)) {
                                    modalReady({
                                        "callback": function (addSiteData) {
                                            console.info(addSiteData);
                                            addSite({
                                                "site_url": $txtSiteURL.val(),
                                                "domain_id": addSiteData.domain_id,
                                                "site_id": addSiteData.site_id,
                                                "domain_price": addSiteData.domain_price,
                                                "product_id": productID,
                                                "comment": addSiteData.comment
                                            }, function (add_site_response) {
                                                if (add_site_response.status == true) {
                                                    loadSingleSite(add_site_response.site.urls.show, function (html) {
                                                        $(el).closest(".tbl-site").find("tbody").prepend(html);
                                                        cancelAddSite($addItemControls.find(".btn-cancel-add-site").get(0));
                                                        updateProductEmptyMessage();
                                                        updateUserSiteUsage(el);
                                                        updateUserSiteUsagePerProduct(el);
                                                    });
                                                } else {
                                                    if (typeof response.errors != 'undefined') {
                                                        var errorMessage = "";
                                                        $.each(response.errors, function (index, error) {
                                                            errorMessage += error + " ";
                                                        });
                                                        alertP("Oops! Something went wrong.", errorMessage);
                                                    } else {
                                                        alertP("Oops! Something went wrong.", "Unable to add site, please try again later.");
                                                    }
                                                }
                                                /*TODO big pb*/
                                            });
                                        }
                                    })
                                }
                            });
                            $modal.on("hidden.bs.modal", function () {
                                $("#modal-site-prices").remove();
                            });
                        },
                        "error": function (xhr, status, error) {
                            hideLoading();
                            if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                                var errorMsg = "";
                                $.each(xhr.responseJSON, function (key, error) {
                                    $.each(error, function (index, message) {
                                        errorMsg += message + " ";
                                    })
                                });
                                alertP("Oops! Something went wrong.", errorMsg);
                            } else {
                                describeServerRespondedError(xhr.status);
                            }
                        }
                    });
                }
            } else {
                hideLoading();
                alertP("Oops! Something went wrong.", 'Unable to add Product Page URL, please try again later.');
            }
        },
        "error": function (xhr, status, error) {
            hideLoading();
            if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                var errorMsg = "";
                $.each(xhr.responseJSON, function (key, error) {
                    $.each(error, function (index, message) {
                        errorMsg += message + " ";
                    })
                });
                alertP("Oops! Something went wrong.", errorMsg);
            } else {
                describeServerRespondedError(xhr.status);
            }
        }
    })
}

function addSite(data, callback) {
    showLoading();
    $.ajax({
        "url": "/site",
        "method": "post",
        "data": data,
        "dataType": "json",
        "success": function (response) {
            hideLoading();
            if ($.isFunction(callback)) {
                callback(response);
            }
        },
        "error": function (xhr, status, error) {
            hideLoading();
            if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                var errorMsg = "";
                $.each(xhr.responseJSON, function (key, error) {
                    $.each(error, function (index, message) {
                        errorMsg += message + " ";
                    })
                });
                alertP("Oops! Something went wrong.", errorMsg);
            } else {
                describeServerRespondedError(xhr.status);
            }
        }
    })
}

function loadSingleSite(url, callback) {
    showLoading();
    $.ajax({
        "url": url,
        "method": "get",
        "success": function (html) {
            hideLoading();

            if ($.isFunction(callback)) {
                callback(html);
            }
        },
        "error": function (xhr, status, error) {
            hideLoading();
            describeServerRespondedError(xhr.status);
        }
    });
}


function btnDeleteProductOnClick(el) {
    deletePopup("Delete Product", "Are you sure you want to delete this Product?",
        "By deleting this product, you will lose the following:",
        [
            "All URLs you have added",
            "All Product charts generated, including any Charts displayed on your Dashboards",
            "All Product Reports generated",
            "All Alerts set up for this Product",
            "This Product's pricing information tracked to date"
        ],
        {
            "affirmative": {
                "text": "DELETE",
                "class": "btn-danger btn-flat",
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
                                gaDeleteProduct();
                                alertP("Delete Product", "Product has been deleted.");
                                updateUserSiteUsage(el);
                                $(el).closest(".product-wrapper").remove();
                                updateUserProductCredit();
                            } else {
                                if (typeof response.errors != 'undefined') {
                                    var errorMessage = "";
                                    $.each(response.errors, function (index, error) {
                                        errorMessage += error + " ";
                                    });
                                    alertP("Oops! Something went wrong.", errorMessage);
                                } else {
                                    alertP("Oops! Something went wrong.", "Unable to delete product, please try again later.");
                                }
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
                "text": "CANCEL",
                "class": "btn-default btn-flat",
                "dismiss": true
            }
        });
}

function toggleEditProductName(el) {
    var $tbl = $(el).closest(".product-wrapper");
    if ($tbl.find(".btn-edit-product").hasClass("editing")) {
        $tbl.find(".btn-edit-product").removeClass("editing").show();
        $tbl.find(".product-name-link").show();
        $tbl.find(".frm-edit-product").hide();
    } else {
        $tbl.find(".btn-edit-product").addClass("editing").hide();
        $tbl.find("input.product-name").val($tbl.find(".product-name-link").text());
        $tbl.find(".product-name-link").hide();
        $tbl.find(".frm-edit-product").show();
        $tbl.find(".frm-edit-product .product-name").focus();
    }
}

function cancelEditProductName(el, event) {
    if (typeof event == 'undefined' || typeof event.keyCode == 'undefined') {
        toggleEditProductName(el);
    } else if (event.keyCode == 27) {
        $(el).blur();
    }
}

function txtProductOnBlur(el) {
    setTimeout(function () {
        if (!$(":focus").is($(el).siblings("span").find("button"))) {
            cancelEditProductName(el);
        }
    }, 10);
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
                gaEditProduct();

                alertP("Update Product", "Product name has been updated.");
                $(el).siblings(".product-name-link").text($(el).find(".product-name").val()).show();
                $(el).hide();
                $(el).closest(".product-wrapper").find(".btn-edit-product.editing").removeClass("editing").show();
            } else {
                alertP("Oops! Something went wrong.", 'Unable to update product name, please try again later.');
            }
        },
        "error": function (xhr, status, error) {
            hideLoading();
            if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                var errorMsg = "";
                $.each(xhr.responseJSON, function (key, error) {
                    $.each(error, function (index, message) {
                        errorMsg += message + " ";
                    })
                });
                alertP("Oops! Something went wrong.", errorMsg);
            } else {
                describeServerRespondedError(xhr.status);
            }
        }
    });
}

function showProductAlertForm(el) {
    showLoading();
    var productID = $(el).closest(".product-wrapper").attr("data-product-id");

    $.ajax({
        "url": $(el).closest(".product-wrapper").attr("data-alert-link"),
        "method": "get",
        "data": {
            "product_id": productID
        },
        "success": function (html) {
            hideLoading();
            var $modal = $(html);
            $modal.modal();
            $modal.on("shown.bs.modal", function () {
                if ($.isFunction(modalReady)) {
                    modalReady({
                        "updateCallback": function (response) {
                            if (response.status == true) {
                                $(el).find("i").removeClass().addClass("fa fa-bell alert-enabled");
                            }
                        },
                        "deleteCallback": function (response) {
                            if (response.status == true) {
                                $(el).find("i").removeClass().addClass("fa fa-bell-o");
                            }
                        }
                    })
                }
            });
            $modal.on("hidden.bs.modal", function () {
                $("#modal-alert-product").remove();
            });
        },
        "error": function (xhr, status, error) {
            hideLoading();
            describeServerRespondedError(xhr.status);
        }
    });
}

function showProductChart(url) {
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

function showProductReportTaskForm(el) {
    showLoading();
    $.ajax({
        "url": $(el).closest(".product-wrapper").attr("data-report-task-link"),
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
                                $(el).find("i").removeClass().addClass("fa fa-envelope ico-report-enabled");
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
                $("#modal-report-task-product").remove();
            });
        },
        "error": function (xhr, status, error) {
            hideLoading();
            describeServerRespondedError(xhr.status);
        }
    });
}

function updateProductEmptyMessage(el) {
    function updateSingleProductEmptyMessage(el) {
        var $tblSite = null;
        if ($(el).hasClass("tbl-site")) {
            $tblSite = $(el);
        } else {
            $tblSite = $(el).find(".tbl-site");
        }

        var $bodyRow = $tblSite.find("tbody > tr").filter(function () {
            return !$(this).hasClass("empty-message-row") && !$(this).hasClass("add-site-row")
        });
        if ($bodyRow.length == 0) {
            $tblSite.find(".empty-message-row").remove();
            $tblSite.find("tbody").prepend(
                $("<tr>").addClass("empty-message-row").append(
                    $("<td>").attr({
                        "colspan": 9
                    }).addClass("text-center").text("To start tracking prices, simply copy and paste the URL of the product page of the website your want to track.")
                )
            )
        } else {
            $tblSite.find(".empty-message-row").remove();
        }
    }

    if (typeof el != 'undefined') {
        updateSingleProductEmptyMessage(el);
    } else {
        $(".tbl-site").each(function () {
            updateSingleProductEmptyMessage(this);
        })
    }
}

function updateUserSiteUsagePerProduct(el) {
    if (!user.isStaff) {
        var $productWrapper = $(el).closest(".product-wrapper");
        $.ajax({
            "url": $productWrapper.attr("data-get-site-usage-per-product-link"),
            "method": "get",
            "dataType": "json",
            "success": function (response) {
                if (response.status == true) {
                    var total = response.total;
                    var usage = response.usage;
                    $productWrapper.find(".lbl-site-usage-per-product").text(usage);
                    $productWrapper.find(".lbl-site-total-per-product").text(total);
                    updateAddSitePanelStatus(usage, total, el);
                }
            },
            "error": function (xhr, status, error) {
                describeServerRespondedError(xhr.status);
            }
        })
    }
}

function updateAddSitePanelStatus(usage, total, el) {
    var $productWrapper = $(el).closest(".product-wrapper");
    var $addSiteContainer = $productWrapper.find(".add-site-container");
    if (usage >= total) {
        $addSiteContainer.attr('onclick', 'appendUpgradeForCreateSiteBlock(this); event.stopPropagation(); return false;');
    } else {
        $addSiteContainer.attr('onclick', 'appendCreateSiteBlock(this); event.stopPropagation(); return false;');
    }
}
function btnDeleteSiteOnClick(el) {
    deletePopup("Delete Product URL", "Are you sure you want to delete the " + $(el).attr("data-name") + "?",
        "By deleting this site, you will lose the following:",
        [
            "All pricing information related to this Site, including any information displayed on your Charts and Dashboards",
            "All Product reports generated",
            "All alerts set up for this Site",
            "This Site's pricing information tracked to date"
        ],
        {
            "affirmative": {
                "text": "DELETE",
                "class": "btn-danger btn-flat",
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
                                gaDeleteSite();
                                alertP("Delete Product URL", "The Product URL has been deleted.");
                                updateUserSiteUsage(el);
                                updateUserSiteUsagePerProduct(el);
                                $(el).closest(".site-wrapper").remove();
                            } else {
                                if (typeof response.errors != 'undefined') {
                                    var errorMessage = "";
                                    $.each(response.errors, function (index, error) {
                                        errorMessage += error + " ";
                                    });
                                    alertP("Oops! Something went wrong.", errorMessage);
                                } else {
                                    alertP("Oops! Something went wrong.", "Unable to delete this Product URL, please try again later.");
                                }
                            }
                            updateProductEmptyMessage();
                        },
                        "error": function (xhr, status, error) {
                            hideLoading();
                            if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                                var errorMsg = "";
                                $.each(xhr.responseJSON, function (key, error) {
                                    $.each(error, function (index, message) {
                                        errorMsg += message + " ";
                                    })
                                });
                                alertP("Oops! Something went wrong.", errorMsg);
                            } else {
                                describeServerRespondedError(xhr.status);
                            }
                        }
                    })
                }
            },
            "negative": {
                "text": "CANCEL",
                "class": "btn-default btn-flat",
                "dismiss": true
            }
        });
}

function toggleEditSiteURL(el) {
    var $tr = $(el).closest(".site-wrapper");
    if ($tr.find(".btn-edit-align-middle").hasClass("editing")) {
        $tr.find(".btn-edit-align-middle").removeClass("editing").show();
        $tr.find(".site-url-link").show();
        $tr.find(".frm-edit-site-url").hide();
    } else {
        $tr.find(".btn-edit-align-middle").addClass("editing").hide();
        $tr.find("input.txt-site-url").val($tr.find(".site-url-link").attr("data-content"));
        $tr.find(".site-url-link").hide();
        $tr.find(".frm-edit-site-url").show();
        $tr.find(".frm-edit-site-url .txt-site-url").focus()
    }
}

function cancelEditSiteURL(el, event) {
    if (typeof event == 'undefined' || typeof event.keyCode == 'undefined') {
        toggleEditSiteURL(el);
    } else if (event.keyCode == 27) {
        $(el).blur();
    }
}

function getPricesEdit(el) {
    var $formEditSiteURL = $(el).closest(".frm-edit-site-url");
    var $txtSiteURL = $formEditSiteURL.find(".txt-site-url");
    var $siteWrapper = $(el).closest(".site-wrapper");
    var siteID = $siteWrapper.attr("data-site-id");
    showLoading();
    $.ajax({
        "url": "/site/prices",
        "method": "post",
        "data": {
            "site_url": $txtSiteURL.val(),
            "site_id": siteID
        },
        "dataType": "json",
        "success": function (response) {
            hideLoading();
            if (typeof response.errors == 'undefined') {
                //PRICE NOT FOUND
                if ((typeof response.sites == 'undefined' || response.sites.length == 0) && typeof response.targetDomain == 'undefined') {
                    editSite({
                        "site_url": $txtSiteURL.val(),
                        "url": $(el).attr("data-url")
                    }, function (edit_site_response) {
                        alertP("Updated Product Page URL", "This price will be updated soon. Note: If it doesn't come up in up to 48 hours, please contact us.");
                        if (edit_site_response.status == true) {
                            loadSingleSite(edit_site_response.site.urls.show, function (html) {
                                toggleEditSiteURL($(el).closest(".site-wrapper").find("btn-edit-site").get(0));
                                $(el).closest(".site-wrapper").replaceWith(html);
                                updateProductEmptyMessage();
                            });
                        } else {
                            alertP("Oops! Something went wrong.", "Unable to edit site, please try again later.");
                        }
                    })
                }
                //PRICE FOUND
                else {
                    showLoading();
                    showSelectPricePopup({
                        "site_url": $txtSiteURL.val()
                    }, function (editSiteData) {
                        editSite({
                            "site_url": $txtSiteURL.val(),
                            "domain_id": editSiteData.domain_id,
                            "site_id": editSiteData.site_id,
                            "domain_price": editSiteData.domain_price,
                            "comment": editSiteData.comment,
                            "url": $(el).attr("data-url")
                        }, function (edit_site_response) {
                            if (edit_site_response.status == true) {
                                loadSingleSite(edit_site_response.site.urls.show, function (html) {
                                    toggleEditSiteURL($(el).closest(".site-wrapper").find("btn-edit-site").get(0));
                                    $(el).closest(".site-wrapper").replaceWith(html);
                                    updateProductEmptyMessage();
                                });
                            } else {
                                alertP("Oops! Something went wrong.", "Unable to edit site, please try again later.");
                            }
                        });
                    });
                }
            } else {
                alertP("Oops! Something went wrong.", 'Unable to update Product Page URL, please try again later.');
            }
        },
        "error": function (xhr, status, error) {
            hideLoading();
            if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                var errorMsg = "";
                $.each(xhr.responseJSON, function (key, error) {
                    $.each(error, function (index, message) {
                        errorMsg += message + " ";
                    })
                });
                alertP("Oops! Something went wrong.", errorMsg);
            } else {
                describeServerRespondedError(xhr.status);
            }
        }
    })
}

function editSite(data, callback) {
    showLoading();
    $.ajax({
        "url": data.url,
        "method": "put",
        "data": data,
        "dataType": "json",
        "success": function (response) {
            hideLoading();
            if ($.isFunction(callback)) {
                callback(response);
            }
        },
        "error": function (xhr, status, error) {
            hideLoading();
            if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                var errorMsg = "";
                $.each(xhr.responseJSON, function (key, error) {
                    $.each(error, function (index, message) {
                        errorMsg += message + " ";
                    })
                });
                alertP("Oops! Something went wrong.", errorMsg);
            } else {
                describeServerRespondedError(xhr.status);
            }
        }
    })
}

function showSelectPricePopup(data, callback) {
    showLoading();
    $.ajax({
        "url": "/site/prices",
        "method": "get",
        "data": data,
        "success": function (html) {
            hideLoading();
            var $modal = $(html);
            $modal.modal();
            $modal.on("shown.bs.modal", function () {
                if ($.isFunction(modalReady)) {
                    modalReady({
                        "callback": function (response) {
                            if ($.isFunction(callback)) {
                                callback(response);
                            }
                        }
                    })
                }
            });
            $modal.on("hidden.bs.modal", function () {
                $("#modal-site-prices").remove();
            });
        },
        "error": function (xhr, status, error) {
            hideLoading();
            describeServerRespondedError(xhr.status);
        }
    });
}


function showSiteAlertForm(el) {
    showLoading();
    $.ajax({
        "url": $(el).closest(".site-wrapper").attr("data-site-alert-url"),
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
                                $(el).find("i").removeClass().addClass("fa fa-bell alert-enabled");
                            }
                        },
                        "deleteCallback": function (response) {
                            if (response.status == true) {
                                $(el).find("i").removeClass().addClass("fa fa-bell-o");
                            }
                        }
                    })
                }
            });
            $modal.on("hidden.bs.modal", function () {
                $("#modal-alert-site").remove();
            });
        },
        "error": function (xhr, status, error) {
            hideLoading();
            describeServerRespondedError(xhr.status);
        }
    });
}

function toggleMyPrice(el) {
    if (($(el).attr("data-product-alert-on-my-price") == 'y' || $(el).attr("data-site-alerts-on-my-price") > 0) && $(el).find("i").hasClass("text-primary")) {
        deletePopup("My Price", "Do you want to disable 'My Price'?",
            "By updating my price, you will lose the following data:",
            [
                "My Price related alerts"
            ],
            {
                "affirmative": {
                    "text": "DELETE",
                    "class": "btn-danger btn-flat",
                    "dismiss": true,
                    "callback": function () {
                        submitToggleMyPrice(el);
                    }
                },
                "negative": {
                    "text": "CANCEL",
                    "class": "btn-default btn-flat",
                    "dismiss": true
                }
            });
    } else {
        submitToggleMyPrice(el);
    }
}

function submitToggleMyPrice(el) {
    var myPrice = $(el).find("i").hasClass("text-primary") ? "n" : "y";
    showLoading();

    $.ajax({
        "url": $(el).closest(".site-wrapper").attr("data-site-update-my-price-url"),
        "method": "put",
        "data": {
            "my_price": myPrice
        },
        "dataType": "json",
        "success": function (response) {
            hideLoading();
            if (response.status == true) {
                gaSetMyPrice();
                showLoading();
                $.ajax({
                    "url": $(el).closest(".site-wrapper").attr("data-site-product-show-url"),
                    "method": "get",
                    "success": function (html) {
                        hideLoading();
                        $(el).closest(".product-wrapper").replaceWith(html);
                    },
                    "error": function (xhr, status, error) {
                        hideLoading();
                        describeServerRespondedError(xhr.status);
                    }
                });
            } else {
                if (typeof response.errors != 'undefined') {
                    var errorMessage = "";
                    $.each(response.errors, function (index, error) {
                        errorMessage += error + " ";
                    });
                    alertP("Oops! Something went wrong.", errorMessage);
                } else {
                    alertP("Oops! Something went wrong.", "unable to set my price, please try again later.");
                }
            }
        },
        "error": function (xhr, status, error) {
            hideLoading();
            describeServerRespondedError(xhr.status);
        }
    })
}

function showSiteChart(url) {
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

function initPopover() {
    $("[data-toggle=popover]").popover();
}

//# sourceMappingURL=product.js.map
