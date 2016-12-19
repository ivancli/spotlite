var tour;

$(function () {
    tour = new Tour({
        steps: [
            {
                element: ".btn-edit-report",
                title: "REPORT SETTINGS",
                content: "You can see all the reports you've set up through this tab. You can edit or delete them easily through here.",
                placement: "top"
            },
            {
                element: "[href='#report-history']",
                title: "REPORT HISTORY",
                content: "Here is the complete log of all the reports sent through to your email address.",
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
        "url": "preference/REPORT_TOUR_VISITED/1",
        "method": "put",
        "dataType": "json",
        "success": function (response) {

        },
        "error": function (xhr, status, error) {

        }
    })
}

function tourNotYetVisit() {
    return user.preferences.REPORT_TOUR_VISITED != 1 && user.preferences.ALL_TOUR_VISITED != 1
}