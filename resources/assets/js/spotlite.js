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
        }
    });

    if (localStorage.getItem("sidebar-is-collapsed-" + user.user_id) == 1) {
        $("body").addClass("sidebar-collapse");
    }

    if (typeof user != 'undefined') {
        /* clean up unused localStorage */
        $.each(localStorage, function (key, value) {
            /*show login welcome message once per day*/
            /*removing previous welcome message local storage*/
            if (key != "met-first-login-welcome-msg-" + today + "-" + user.user_id && key.startsWith("met-first-login-welcome-msg-")) {
                localStorage.removeItem(key);
            }
            /*show credit card expiry notification once per day*/
            /*removing previous credit card expiry message local storage*/
            if (key != "met-cc-expiry-msg-" + today + "-" + user.user_id && key.startsWith("met-cc-expiry-msg-")) {
                localStorage.removeItem(key);
            }
        });

        if ((typeof user.preferences == 'undefined' || user.preferences.DONT_SHOW_WELCOME != 1) && localStorage.getItem("met-first-login-welcome-msg-" + today + "-" + user.user_id) != 1) {
            showLoading();
            showWelcomePopup(function () {
                $("video").get(0).pause();

                /*if bootstrap tour is available in this page*/
                if (typeof tour != 'undefined' && $.isFunction(tourNotYetVisit) && tourNotYetVisit()) {
                    startTour();
                    setTourVisited();
                } else if (typeof cc_expire_within_a_month != 'undefined' && cc_expire_within_a_month == true && localStorage.getItem("met-cc-expiry-msg-" + today + "-" + user.user_id) != 1) {
                    /*or if the credit card will be expire soon, show notification*/
                    localStorage.setItem("met-cc-expiry-msg-" + today + "-" + user.user_id, 1);
                    showCreditCardExpiry();
                }
            });
        } else {
            if (typeof tour != 'undefined' && $.isFunction(tourNotYetVisit) && tourNotYetVisit()) {
                startTour();
                setTourVisited();
            } else if (typeof cc_expire_within_a_month != 'undefined' && cc_expire_within_a_month == true && localStorage.getItem("met-cc-expiry-msg-" + today + "-" + user.user_id) != 1) {
                localStorage.setItem("met-cc-expiry-msg-" + today + "-" + user.user_id, 1);
                showCreditCardExpiry();
            }
        }
    } else {
        if (typeof tour != 'undefined' && $.isFunction(tourNotYetVisit) && tourNotYetVisit()) {
            startTour();
            setTourVisited();
        } else if (typeof cc_expire_within_a_month != 'undefined' && cc_expire_within_a_month == true && localStorage.getItem("met-cc-expiry-msg-" + today + "-" + user.user_id) != 1) {
            localStorage.setItem("met-cc-expiry-msg-" + today + "-" + user.user_id, 1);
            showCreditCardExpiry();
        }
    }
});

function showWelcomePopup(callback) {
    $.get('/msg/subscription/welcome/0', function (html) {
        localStorage.setItem("met-first-login-welcome-msg-" + today + "-" + user.user_id, 1);
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

function saveSidebarStatus() {
    if ($("body").hasClass("sidebar-collapse")) {
        localStorage.removeItem("sidebar-is-collapsed-" + user.user_id);
    } else {
        localStorage.setItem("sidebar-is-collapsed-" + user.user_id, 1);
    }
}