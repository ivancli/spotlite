var tour;

$(function () {
    tour = new Tour({
        steps: [
            {
                element: ".add-category-container",
                title: "ADD CATEGORY",
                content: "Start with naming the Category. You can add multiple Categories. More Category examples: Running Shoes, Eye Liner or Books.",
                placement: "top"
            },
            {
                element: ".add-product-container:first",
                title: "ADD PRODUCT",
                content: "Now add Products within the Category. You can add multiple products. More product examples, considering the Running Shoes Category: Nike Zoom, Mizuno Wave Raider.",
                placement: "top"
            },
            {
                element: ".add-site-container:first",
                title: "ADD PRODUCT PAGE URL",
                content: "Last step of Products set up! Just add each Product Page URLs you want to track. Go into the webpage where the product price is, copy the whole URL and paste it here.",
                placement: "top"
            },
            {
                element: ".btn-delete-category:first",
                title: "DELETE",
                content: "You can delete Categories & Products easily through here.",
                placement: "bottom"
            },
            {
                element: ".btn-report:first",
                title: "EMAIL REPORTS",
                content: "Set your Email reports at your preferred frequency and time. We'll deliver the report directly to your inbox!",
                placement: "bottom"
            },
            {
                element: ".btn-chart:first",
                title: "CHARTS",
                content: "Create Category & Product Charts based on a period (e.g. month) and granularity (e.g. day). You can also add it to your Dashboard to easily visualise past and current price trends. Once you've added a Chart to your Dashboard, it will be automatically updated if any price change occurs.",
                placement: "left"
            },
            {
                element: "#btn-set-up-alerts",
                title: "ALERT NOTIFICATIONS",
                content: "You can now set up Basic Alerts across all Categories or Advanced Alerts in specific Categories and/or Products so you're notified of price changes immediately.",
                placement: "left"
            },
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
        "url": "preference/PRODUCT_TOUR_VISITED/1",
        "method": "put",
        "dataType": "json",
        "success": function (response) {

        },
        "error": function (xhr, status, error) {

        }
    })
}

function tourNotYetVisit() {
    return user.preferences.PRODUCT_TOUR_VISITED != 1 && user.preferences.ALL_TOUR_VISITED != 1
}