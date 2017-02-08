function gaSendEvent(eventCategory, eventAction, eventLabel, eventValue, fieldsObject) {
    eventCategory = typeof eventCategory != "undefined" ? eventCategory : null;
    eventAction = typeof eventAction != "undefined" ? eventAction : null;
    eventLabel = typeof eventLabel != "undefined" ? eventLabel : null;
    eventValue = typeof eventValue != "undefined" ? eventValue : null;
    fieldsObject = typeof fieldsObject != "undefined" ? fieldsObject : null;
    ga('send', 'event', eventCategory, eventAction, eventLabel, eventValue, fieldsObject)
}

function gaFormatParams(params) {
    var label = "";
    $.each(params, function (index, value) {
        label += index + "=" + value + ":";
    });
    label = label.slice(0, -1);
    return label;
}

/**********************************************************
 * auth
 **********************************************************/

function gaForgotPassword() {
    gaSendEvent("Login", "Forgot Password")
}

function gaLogin() {
    gaSendEvent("Login", "Click Login");
}

function gaRegister() {
    gaSendEvent("Login", "Register Account");
}

/**********************************************************
 * dashboard
 **********************************************************/

function gaDisplayDashboard() {
    gaSendEvent("Dashboard", "Display Dashboard");
}

function gaDashboardApplyFilter(params) {
    var label = gaFormatParams(params)
    gaSendEvent("Dashboard", "Apply Filters", label);
}

function gaDashboardAddContent(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Dashboard", "Apply Filters", label);
}

/**********************************************************
 * category
 **********************************************************/

function gaAddCategory() {
    gaSendEvent("Products", "Add Category");
}

function gaEditCategory() {
    gaSendEvent("Products", "Edit Category");
}

function gaDeleteCategory() {
    gaSendEvent("Products", "Delete Category");
}

function gaMoveCategory() {
    gaSendEvent("Products", "Move Category");
}

function gaCategoryReport(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Reports", "Category Report", label);
}

function gaGenerateCategoryChart(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Products", "Generate Category Chart", label);
}

function gaAddCategoryChartToDashboard(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Products", "Add Category Chart to Dashboard", label);
}

/**********************************************************
 * product
 **********************************************************/

function gaAddProduct() {
    gaSendEvent("Products", "Add Product");
}

function gaEditProduct() {
    gaSendEvent("Products", "Edit Product");
}

function gaDeleteProduct() {
    gaSendEvent("Products", "Delete Product");
}

function gaMoveProduct() {
    gaSendEvent("Products", "Move Product");
}

function gaMoveSite() {
    gaSendEvent("Products", "Move Site");
}

function gaAddProductReport(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Reports", "Add Product Report", label);
}

function gaAddProductAlert(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Alerts", "Add Product Alert", label);
}

function gaGenerateProductChart(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Products", "Generate Product Chart", label);
}

function gaAddProductChartToDashboard(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Products", "Add Product Chart to Dashboard", label);
}

/**********************************************************
 * site
 **********************************************************/

function gaAddSite() {
    gaSendEvent("Products", "Add Site");
}

function gaEditSite() {
    gaSendEvent("Products", "Edit Site");
}

function gaDeleteSite() {
    gaSendEvent("Products", "Delete Site");
}

function gaSetMyPrice() {
    gaSendEvent("Products", "Set My Price");
}

function gaSiteAlert(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Alerts", "Site Alert", params);
}

function gaGenerateSiteChart(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Products", "Generate Site Chart", label);
}

function gaAddSiteChartToDashboard(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Products", "Add SiteChart to Dashboard", label);
}

/**********************************************************
 * reports
 **********************************************************/

function gaDeleteReportFromReportsPage(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Reports", "Delete Report", label);
}

function gaEditReportFromReportsPage(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Reports", "Edit Report", label);
}

/**********************************************************
 * alerts
 **********************************************************/

function gaDeleteAlertFromAlertsPage(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Alerts", "Delete Alert", label);
}

function gaEditAlertFromAlertsPage(params) {
    var label = gaFormatParams(params);
    gaSendEvent("Alerts", "Edit Alert", label);
}

/**********************************************************
 * settings
 **********************************************************/

function gaUpdateDateTime() {
    gaSendEvent("Settings", "Update Date Time");
}

function gaUpdateUserProfile() {
    gaSendEvent("Settings", "Update User Profile");
}

function gaResetPassword() {
    gaSendEvent("Settings", "Reset Password");
}

function gaLogout() {
    gaSendEvent("Settings", "Logout");
}