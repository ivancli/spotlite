var tour;

$(function () {
    tour = new Tour({
        steps: [
            {
                element: ".btn-edit-alert",
                title: "ALERT SETTINGS",
                content: "You can see all the alerts you've set up through this tab. You can edit or delete them easily through here.",
                placement: "top"
            },
            {
                element: "[href='#alert-history']",
                title: "ALERT HISTORY",
                content: "Here is the complete log of all the alerts sent through to your email address.",
                placement: "top"
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
        "url": "preference/ALERT_TOUR_VISITED/1",
        "method": "put",
        "dataType": "json",
        "success": function (response) {

        },
        "error": function (xhr, status, error) {

        }
    })
}

function tourNotYetVisit() {
    return user.preferences.ALERT_TOUR_VISITED != 1 && user.preferences.ALL_TOUR_VISITED != 1
}