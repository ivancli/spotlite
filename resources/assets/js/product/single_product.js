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
            return !$(this).hasClass("empty-message-row") && !$(this).hasClass("add-site-row") && !$(this).hasClass("spinner-row") && !$(this).hasClass("load-more-site");
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
    if (user.needSubscription) {
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