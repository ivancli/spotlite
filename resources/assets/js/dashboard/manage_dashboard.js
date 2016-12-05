/**
 * Created by ivan.li on 12/5/2016.
 */

function showAddDashboardForm(el) {
    showLoading();
    $.ajax({
        "url": '/dashboard/create',
        "method": 'get',
        "success": function (html) {
            hideLoading();
            var $modal = $(html);
            $modal.modal();
            $modal.on("shown.bs.modal", function () {
                if ($.isFunction(modalReady)) {
                    modalReady({
                        "callback": function (response) {
                            if (response.status == true && typeof response.dashboard != 'undefined') {
                                window.location.href = response.dashboard.urls.show;
                            }
                        }
                    })
                }
            });
            $modal.on("hidden.bs.modal", function () {
                $("#modal-dashboard-store").remove();
            });
        },
        "error": function (xhr, status, error) {
            hideLoading();
            describeServerRespondedError(xhr.status);
        }
    })
}

function showEditDashboardForm(el) {
    showLoading();
    $.ajax({
        "url": $(el).attr("data-url"),
        "method": 'get',
        "success": function (html) {
            hideLoading();
            var $modal = $(html);
            $modal.modal();
            $modal.on("shown.bs.modal", function () {
                if ($.isFunction(modalReady)) {
                    modalReady({
                        "callback": function (response) {
//                                    tblDashboard.ajax.reload();
                            window.location.reload();
                        }
                    })
                }
            });
            $modal.on("hidden.bs.modal", function () {
                $("#modal-dashboard-update").remove();
            });
        },
        "error": function (xhr, status, error) {
            hideLoading();
            describeServerRespondedError(xhr.status);
        }
    })
}

function deleteDashboard(el) {
    confirmP("Delete dashboard", "Do you want to delete this dashboard?", {
        "affirmative": {
            "text": "Delete",
            "class": "btn-danger btn-flat",
            "dismiss": true,
            "callback": function () {
                showLoading();
                $.ajax({
                    "url": $(el).attr("data-url"),
                    "method": "delete",
                    "dataType": "json",
                    "success": function (response) {
                        hideLoading();
                        if (response.status == true) {
//                                    tblDashboard.row($(el).closest("tr")).remove().draw();
                            window.location.href = "/";
                        } else {
                            alertP("Error", "Unable to delete dashboard, please try again later.");
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
            "text": "Cancel",
            "class": "btn-default btn-flat",
            "dismiss": true
        }
    })
}