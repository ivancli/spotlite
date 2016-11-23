/**
 * Created by ivan.li on 11/23/2016.
 */
function trapError(errorMsg, url, lineNumber, column, errorObj) {
// From https://danlimerick.wordpress.com/2014/01/18/how-to-catch-javascript-errors-with-window-onerror-even-on-chrome-and-firefox/
    var postData = {
        "errorMsg": errorMsg,
        "url": url,
        "lineNumber": lineNumber,
        "column": column,
        "user.email": user.email,
        "user.name": user.first_name + " " + user.last_name,
        "content": 'Error: ' + errorMsg + ' Script: ' + url + ' Line: ' + lineNumber + ' Column: ' + column + ' StackTrace: ' + errorObj
    };

    var postStr = "";
    for (var key in postData) {
        if (postStr != "") {
            postStr += "&";
        }
        postStr += key + "=" + encodeURIComponent(postData[key]);
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', encodeURI('/error/notify_error'));
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector("meta[name=csrf-token]").content);
    xhr.send(postStr);
    return false;
}

if (typeof window.onerror != 'undefined') {
    window.onerror = trapError;
}