Highcharts.setOptions({
    global: {
        useUTC: false
    }
});

var today = timestampToDateTimeByFormat(new Date().getTime() / 1000, 'Y-m-d');

$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        cache: false
    });

    // if (localStorage.getItem("sidebar-is-collapsed-" + user.user_id) == 1) {
    //     $("body").addClass("sidebar-collapse");
    // }

    // if (getLocalStorageOrCookie("sidebar-is-collapsed-" + user.user_id) == 1) {
    //     $("body").addClass("sidebar-collapse");
    // }

    if (typeof user != 'undefined' && (user.needSubscription == false || (user.subscription != null && user.subscription.cancelled_at == null))) {
        /* clean up unused cookie/localStorage */
        if (localStorageAvailable()) {
            $.each(localStorage, function (key, value) {
                /*show login welcome message once per day*/
                /*removing previous welcome message local storage*/
                if (key != "met-first-login-welcome-msg-" + today + "-" + user.user_id && key.indexOf("met-first-login-welcome-msg-") > -1) {
                    localStorage.removeItem(key);
                }
                /*show credit card expiry notification once per day*/
                /*removing previous credit card expiry message local storage*/
                if (key != "met-cc-expiry-msg-" + today + "-" + user.user_id && key.indexOf("met-cc-expiry-msg-") > -1) {
                    localStorage.removeItem(key);
                }
            });
        } else {
            $.each(getCookies(), function (key, value) {
                /*show login welcome message once per day*/
                /*removing previous welcome message local storage*/
                if (key != "met-first-login-welcome-msg-" + today + "-" + user.user_id && key.indexOf("met-first-login-welcome-msg-") > -1) {
                    removeCookie(key);
                }
                /*show credit card expiry notification once per day*/
                /*removing previous credit card expiry message local storage*/
                if (key != "met-cc-expiry-msg-" + today + "-" + user.user_id && key.indexOf("met-cc-expiry-msg-") > -1) {
                    removeCookie(key);
                }
            });
        }

        if (user.set_password == 'n') {
            showSetPasswordPopup(function () {
                showLoading();
                showWelcomePopup(function () {
                    if ($("video").length > 0) {
                        $("video").get(0).pause();
                    }
                    tourOrCreditCard();
                });
            })
        } else {
            if (user.industry == null) {
                showLoading();
                showWelcomePopup(function () {
                    if ($("video").length > 0) {
                        $("video").get(0).pause();
                    }
                    tourOrCreditCard();
                });
            } else {
                tourOrCreditCard();
            }
        }
    } else {
    }
});

function showSetPasswordPopup(callback) {
    showLoading();
    $.ajax({
        "url": "/password/init_reset",
        "success": function (html) {
            hideLoading();
            var $modal = popupFrame(html);
            $modal.find(".modal-dialog");
            $modal.modal({
                "backdrop": "static",
                "keyboard": false
            });

            $modal.on("hidden.bs.modal", function () {
                if ($.isFunction(callback)) {
                    callback();
                }
            });
        },
        "error": function (xhr, status, error) {
            hideLoading();
            describeServerRespondedError(xhr.status);
        }
    })
}

function showWelcomePopup(callback) {
    $.get('/msg/subscription/welcome/0', function (html) {
        setLocalStorageOrCookie("met-first-login-welcome-msg-" + today + "-" + user.user_id, 1);
        hideLoading();
        var $modal = popupFrame(html);
        $modal.find(".modal-dialog").addClass("modal-lg");
        $modal.modal({
            "backdrop": "static",
            "keyboard": false
        });

        $modal.on("hidden.bs.modal", function () {
            if ($.isFunction(callback)) {
                callback();
            }
        });
    });
}

function showCreateGroupFirstLogin() {
    showLoading();
    $.get('group/first_login', function (html) {
        hideLoading();
        var $modal = $(html);
        $modal.modal({
            "backdrop": "static",
            "keyboard": false
        });
        $modal.on("hidden.bs.modal", function () {
            $("#modal-group-store").remove();
        });
    });
}

function showCreditCardExpiry() {
    showLoading();
    $.get('/msg/subscription/cc_expiring/0', function (html) {
        hideLoading();
        var $modal = popupFrame(html);
        $modal.modal();
    });
}

// function saveSidebarStatus() {
//     if ($("body").hasClass("sidebar-collapse")) {
//         removeLocalStorageOrCookie("sidebar-is-collapsed-" + user.user_id);
//     } else {
//         setLocalStorageOrCookie("sidebar-is-collapsed-" + user.user_id, 1);
//     }
// }

function tourOrCreditCard() {
    /*if bootstrap tour is available in this page*/
    if ($.urlParam('auto_tour') == 'true' && typeof tour != 'undefined') {
        startTour();
        setTourVisited();
    } else if (typeof tour != 'undefined' && $.isFunction(tourNotYetVisit) && tourNotYetVisit()) {
        startTour();
        setTourVisited();
    } else if (typeof cc_expire_within_a_month != 'undefined' && cc_expire_within_a_month == true && getLocalStorageOrCookie("met-cc-expiry-msg-" + today + "-" + user.user_id) != 1) {
        /*or if the credit card will be expire soon, show notification*/
        setLocalStorageOrCookie("met-cc-expiry-msg-" + today + "-" + user.user_id, 1);
        showCreditCardExpiry();
    }
}

function showContactUs() {
    showLoading();
    $.get('/contact_us/form', function (html) {
        hideLoading();
        var $modal = $(html);
        $modal.modal();
        $modal.on("hidden.bs.modal", function () {
            $("#modal-contact-us").remove();
        });
    });
}


function startSpotLiteTour(el) {
    if (user.firstAvailableDashboard != null) {
        window.location.href = user.firstAvailableDashboard.urls.show + "?auto_tour=true";
    } else {
        removeLocalStorageOrCookie('tour_current_step');
        removeLocalStorageOrCookie('tour_end');
        startTour();
        setTourVisited();
    }
}
//# sourceMappingURL=spotlite.js.map
