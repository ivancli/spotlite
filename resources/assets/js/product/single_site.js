function btnDeleteSiteOnClick(el) {
    deletePopup("Delete Site", "Are you sure you want to delete the " + $(el).attr("data-name") + "?",
        "By deleting this site, you will lose the following:",
        [
            "All pricing information related to this Site, including any information displayed on your Charts and Dashboards",
            "All Product reports generated",
            "All alerts set up for this Site",
            "This Site's pricing information tracked to date"
        ],
        {
            "affirmative": {
                "text": "Delete",
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
                                alertP("Delete Site", "The site has been deleted.");
                                updateUserSiteUsage(el);
                                updateUserSiteUsagePerProduct(el);
                                $(el).closest(".site-wrapper").remove();
                            } else {
                                alertP("Error", "Unable to delete site, please try again later.");
                            }
                            updateProductEmptyMessage();
                        },
                        "error": function (xhr, status, error) {
                            hideLoading();
                            describeServerRespondedError(xhr.status);
                        }
                    })
                }
            },
            "negative": {
                "text": "Cancel",
                "class": "btn-default btn-flat",
                "dismiss": true
            }
        });
}

// function toggleEditSiteURL(el) {
//     var $tr = $(el).closest(".site-wrapper");
//     if ($(el).hasClass("editing")) {
//         $(el).removeClass("editing");
//         $tr.find(".site-url-link").show();
//         $tr.find(".frm-edit-site-url").hide();
//     } else {
//         $tr.find(".site-url-link").hide();
//         $tr.find(".frm-edit-site-url").show();
//         $tr.find(".frm-edit-site-url .txt-site-url").focus();
//         $(el).addClass("editing");
//     }
// }


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
        "method": "get",
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
                        if (edit_site_response.status == true) {
                            loadSingleSite(edit_site_response.site.urls.show, function (html) {
                                toggleEditSiteURL($(el).closest(".site-wrapper").find("btn-edit-site").get(0));
                                $(el).closest(".site-wrapper").replaceWith(html);
                                updateProductEmptyMessage();
                            });
                        } else {
                            alertP("Error", "Unable to add site, please try again later.");
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
                            "url": $(el).attr("data-url")
                        }, function (edit_site_response) {
                            if (edit_site_response.status == true) {
                                loadSingleSite(edit_site_response.site.urls.show, function (html) {
                                    toggleEditSiteURL($(el).closest(".site-wrapper").find("btn-edit-site").get(0));
                                    $(el).closest(".site-wrapper").replaceWith(html);
                                    updateProductEmptyMessage();
                                });
                            } else {
                                alertP("Error", "Unable to add site, please try again later.");
                            }
                        });
                    });
                }
            } else {
                var errorMsg = "Unable to edit site. ";
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
            describeServerRespondedError(xhr.status);
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
                    "text": "Delete",
                    "class": "btn-danger btn-flat",
                    "dismiss": true,
                    "callback": function () {
                        submitToggleMyPrice(el);
                    }
                },
                "negative": {
                    "text": "Cancel",
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
                alertP("Error", "unable to set my price, please try again later.");
            }
        },
        "error": function () {
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
