var tour;

$(function () {
    tour = new Tour({
        steps: [
            {
                element: "#btn-add-new-dashboard",
                title: "YOUR DASHBOARDS",
                content: "You can view your Dashboards or add a new one anytime through the menu navigation.",
                onShown: function (tour) {
                    $(".tour-step-background").append(
                        $("<div>").css({
                            "height": "100%",
                            "padding": "20px"
                        }).append(
                            $("<a>").addClass("tour-step-backdrop tour-tour-element tour-tour-0-element").attr({
                                "href": "#",
                                "onclick": "showAddDashboardForm(this); return false;",
                                "id": "btn-add-new-dashboard"
                            }).css({
                                "background-color": "#7ed0c0",
                                "height": "100%",
                                "display": "block",
                                "padding": "10px",
                                "color": "#fff"
                            }).append(
                                $("<i>").addClass("fa fa-plus"),
                                $("<span>").text(" ADD A NEW DASHBOARD")
                            )
                        )
                    )
                }
            },
            {
                element: "#btn-dropdown-manage-dashboard",
                title: "MANAGE DASHBOARD",
                content: "This is your Default Dashboard with Sample Data. You can Add a Chart, Rename or Delete a Dashboard through this menu."
            },
            {
                element: ".widget-container:first .box-tools",
                title: "CHART CONFIGURATION",
                content: "You can view information about this Chart, download it, edit or delete it through these icons.",
                placement: "bottom"
            },
            {
                element: ".widget-container:first .box-tools .btn-edit-widget",
                title: "EDIT CHART",
                content: "You can change the Chart type category and product, time span, resolution period and name.",
                placement: "bottom"
            },
            {
                element: ".add-chart-to-dashboard-placeholder",
                title: "ADD CHART TO DASHBOARD",
                content: "You can choose your own Chart characteristic and add it to the Dashboard",
                placement: "right"
            },
            {
                element: ".lnk-product",
                title: "ADD YOUR PRODUCTS",
                content: "If you want to add your own products to track, let's go to the PRODUCTS page and get started.",
                placement: "right",
                path: "/dashboard",
                onShown: function () {
                    $(".tour-step-background").append(
                        $("<div>").css({
                            "height": "100%",
                            "padding": "20px"
                        }).append(
                            $("<a>").addClass("tour-step-backdrop tour-tour-element tour-tour-0-element").attr({
                                "href": "http://app.spotlite.dev/product"
                            }).css({
                                "background-color": "#7ed0c0",
                                "height": "100%",
                                "display": "block",
                                "padding": "10px",
                                "color": "#fff"
                            }).append(
                                $("<i>").addClass("fa fa-tag"),
                                $("<span>").text(" PRODUCTS")
                            )
                        )
                    )
                }
            }
        ],
        backdrop: true,
        storage: window.localStorage,
        backdropPadding: 20
    });
    tour.init();
});

function startTour() {
    tour.restart();
}

function setTourVisited() {
    $.ajax({
        "url": "preference/ALL_TOUR_VISITED/1",
        "method": "put",
        "dataType": "json",
        "success": function (response) {

        },
        "error": function (xhr, status, error) {

        }
    })
}

function tourNotYetVisit() {
    return user.preferences.ALL_TOUR_VISITED != 1 && user.preferences.PRODUCT_TOUR_VISITED != 1 && user.preferences.DASHBOARD_TOUR_VISITED != 1
}