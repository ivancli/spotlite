$(function () {
    if (typeof user != 'undefined' && typeof user.is_first_login != 'undefined' && user.subscriptions.length > 0) {
        if (user.is_first_login == 'y' && localStorage.getItem("met-first-login-welcome-msg-" + user.user_id) != 1) {
            showLoading();
            /*TODO show first login welcome message*/
            $.get('/msg/subscription/welcome/0', function (html) {
                hideLoading();
                console.info(html);
                var $modal = popupFrame(html);
                $modal.modal();
                $modal.on("hidden.bs.modal", showCreateGroupFirstLogin);
                localStorage.setItem("met-first-login-welcome-msg-" + user.user_id, 1);
            });
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