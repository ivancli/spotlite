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
    var $modal = popupHTML(title, bodyText, $footer);
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
    if (typeof $footer == 'undefined') {
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
    return $("<div>").attr("id", randomString(10)).addClass("modal fade popup").append(
        $("<div>").addClass("modal-dialog " + dialogSize).append(
            $("<div>").addClass("modal-content").append(
                $("<div>").addClass("modal-header").append(
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
                ),
                $("<div>").addClass("modal-body").append($content),
                $("<div>").addClass("modal-footer m-0").append($footer)
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
