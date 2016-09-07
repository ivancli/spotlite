function welcome(bodyText) {
    var $modal = popupHTML(title, bodyText, null, "lg");
    $modal.modal();
}

/**
 * simulate alert popup
 * @param title
 * @param bodyText
 */
function alertP(title, bodyText) {
    var $modal = popupHTML(title, bodyText, null, "sm");
    $modal.modal();
}

/**
 * simulate confirm popup
 * @param title
 * @param bodyText
 * @param btnOpts
 */
function confirmP(title, bodyText, btnOpts) {
    var $footer = $("<div>").append(
        $("<button>")
            .addClass("btn")
            .addClass(typeof btnOpts.affirmative.class == 'undefined' ? "" : btnOpts.affirmative.class)
            .on("click", function () {
                if (typeof btnOpts.affirmative.callback != 'undefined' && $.isFunction(btnOpts.affirmative.callback)) {
                    btnOpts.affirmative.callback();
                }
            })
            .attr("data-dismiss", function () {
                return typeof btnOpts.affirmative.dismiss != 'undefined' && btnOpts.affirmative.dismiss == true ? "modal" : "";
            })
            .text(typeof btnOpts.affirmative.text != 'undefined' ? btnOpts.affirmative.text : 'OK'),
        $("<button>")
            .addClass("btn")
            .addClass(typeof btnOpts.negative.class == 'undefined' ? "" : btnOpts.negative.class)
            .on("click", function () {
                if (typeof btnOpts.negative.callback != 'undefined' && $.isFunction(btnOpts.negative.callback)) {
                    btnOpts.negative.callback();
                }
            })
            .attr("data-dismiss", function () {
                return typeof btnOpts.negative.dismiss != 'undefined' && btnOpts.negative.dismiss == true ? "modal" : "";
            })
            .text(typeof btnOpts.negative.text != 'undefined' ? btnOpts.negative.text : 'Cancel')
    );
    var $modal = popupHTML(title, bodyText, $footer, "sm");
    $modal.modal();
}

/**
 * Create popup HTML content
 * @param title
 * @param $content
 * @param $footer
 * @param dialogSize
 * @returns {*|jQuery}
 */
function popupHTML(title, $content, $footer, dialogSize) {
    var $header = $("<div>").append(
        $("<button>").addClass("close").attr({
            "type": "button",
            "data-dismiss": "modal",
            "aria-label": "Close"
        }).append(
            $("<span>").attr({
                "aria-hidden": "true"
            }).html("&times;")
        ),
        $("<h4>").addClass("modal-title").text(title)
    );


    if (typeof $footer == 'undefined' || $footer == null) {
        $footer = $("<button>").addClass("btn").attr({
            "type": "button",
            "data-dismiss": "modal"
        }).text("OK");
    }
    if (typeof dialogSize == "undefined") {
        dialogSize = "";
    } else {
        switch (dialogSize) {
            case "lg":
                dialogSize = "modal-lg";
                break;
            case "md":
                dialogSize = "";
                break;
            case "sm":
                dialogSize = "modal-sm";
                break;
            default:
        }
    }
    var $modal = popupFrame($header, $content, $footer);
    $modal.find(".modal-dialog").addClass(dialogSize);
    return $modal;
}

function popupFrame($header, $content, $footer) {
    return $("<div>").attr("id", randomString(10)).addClass("modal fade popup").append(
        $("<div>").addClass("modal-dialog").append(
            $("<div>").addClass("modal-content").append(
                typeof $header != 'undefined' ?
                    $("<div>").addClass("modal-header").append(
                        $header
                    ) : '',
                typeof $content != 'undefined' ?
                    $("<div>").addClass("modal-body").append(
                        $content
                    ) : '',
                typeof $footer != 'undefined' ?
                    $("<div>").addClass("modal-footer").append(
                        $footer
                    ) : ''
            )
        )
    );
}

/**
 * Generate random string
 * @param length
 * @param chars
 * @returns {string}
 */
function randomString(length, chars) {
    if (typeof chars == 'undefined') {
        chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
    return result;
}

function showLoading() {
    var $spinner = $("<div>").addClass("spinner").append(
        $("<div>").addClass("spinner-backdrop"),
        $("<img>").attr({
            "src": "/build/images/spinner.gif"
        }).addClass("spinner-img")
    );
    $("body").append($spinner);
}

function hideLoading() {
    $(".spinner").remove();
}

Number.prototype.formatMoney = function(c, d, t){
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};