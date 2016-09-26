$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    var today = timestampToDateTimeByFormat(new Date().getTime() / 1000, 'Y-m-d');



    if (typeof user != 'undefined' && typeof user.is_first_login != 'undefined' && user.subscription != null) {
        /* clean up unused localStorage */
        $.each(localStorage, function (key, value) {
            if (key != "met-first-login-welcome-msg-" + today + "-" + user.user_id && key.startsWith("met-first-login-welcome-msg-")) {
                localStorage.removeItem(key);
            }
            if (key != "met-cc-expiry-msg-" + today + "-" + user.user_id && key.startsWith("met-cc-expiry-msg-")) {
                localStorage.removeItem(key);
            }
        });
        if ((typeof user.preferences == 'undefined' || user.preferences.DONT_SHOW_WELCOME != 1)
            && user.is_first_login == 'y'
            && localStorage.getItem("met-first-login-welcome-msg-" + today + "-" + user.user_id) != 1) {
            showLoading();
            /*TODO show first login welcome message*/
            $.get('/msg/subscription/welcome/0', function (html) {
                hideLoading();
                var $modal = popupFrame(html);
                $modal.modal();
                // $modal.on("hidden.bs.modal", showCreateGroupFirstLogin);

                $modal.on("hidden.bs.modal", function () {
                    if (cc_expire_within_a_month == true && localStorage.getItem("met-cc-expiry-msg-" + today + "-" + user.user_id) != 1) {
                        localStorage.setItem("met-cc-expiry-msg-" + today + "-" + user.user_id, 1);
                        showCreditCardExpiry();
                    }
                });
                localStorage.setItem("met-first-login-welcome-msg-" + today + "-" + user.user_id, 1);
            });
        }
    } else {
        if (typeof cc_expire_within_a_month != 'undefined' && cc_expire_within_a_month == true && localStorage.getItem("met-cc-expiry-msg-" + today + "-" + user.user_id) != 1) {
            localStorage.setItem("met-cc-expiry-msg-" + today + "-" + user.user_id, 1);
            showCreditCardExpiry();
        }
    }
});

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