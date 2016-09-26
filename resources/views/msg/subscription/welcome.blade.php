<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <!--<h4 class="modal-title" id="myModalLabel">Welcome to Composer!</h4>-->
    <div class="row">
        <div class="col-sm-12">
            <h2 class="text-center">
                {{auth()->user()->first_name}}, Welcome to
            </h2>
            <h3 class="text-center">
                <img src="{{asset('images/logo-fixed-2.png')}}" style="width: 30%;">
            </h3>

            <p class="text-center">
                So nice to meet you!
            </p>
            <p class="text-center">
                It's time to let SpotLite do the hard work while you focus on what matters: growing your business.
            </p>
            <p class="text-center">
                Here are a few handy guides you might find useful to get you quickly set up!
            </p>
            <p class="text-center">
                You can watch our video tutorial
            </p>
            <div class="m-b-10">
                <video width="100%" controls preload="auto">
                    <source src="{{asset('videos/sample_video.mp4')}}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td width="33%" style="vertical-align: bottom;" align="center">
                                <div class="hidden-xs">Download the tutorial</div>
                                <div>
                                    <a href="{{asset('videos/sasmple')}}" class="text-muted">
                                        <div style="font-size: 25px;">
                                            <i class=" fa fa-download"></i>
                                        </div>
                                        <div class="hidden-xs" style="font-weight: bold; color: #3ba83d;">
                                            GET IT NOW
                                        </div>
                                    </a>
                                </div>
                            </td>
                            <td width="33%" style="vertical-align: bottom;" align="center">
                                <div class="hidden-xs">Check the FAQ</div>
                                <div>
                                    <a href="#" class="text-muted">
                                        <div style="font-size: 25px;">
                                            <i class=" fa fa-question-circle-o"></i>
                                        </div>
                                        <div class="hidden-xs" style="font-weight: bold; color: #3ba83d;">
                                            TAKE ME THERE
                                        </div>
                                    </a>
                                </div>
                            </td>
                            <td width="34%" style="vertical-align: bottom;" align="center">
                                <div class="hidden-xs">No need, thanks</div>
                                <div class="checkbox" style="font-size: 9px; margin-bottom: 0px;">
                                    <label>
                                        <input type="checkbox" onclick="updateDontShowWelcomePage(this)"
                                               style="margin-top: 0;">
                                        Don't show me this message again, please!
                                    </label>
                                </div>
                                <div>
                                    <a href="{{route('dashboard.index')}}" class="text-muted">
                                        <div style="font-size: 25px;">
                                            <img src="{{asset('images/favicon.png')}}" alt="" width="30">
                                        </div>
                                        <div class="hidden-xs" style="font-weight: bold; color: #3ba83d;">GO TO MY
                                            DASHBOARD
                                        </div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {

        });

        function updateDontShowWelcomePage(el) {
            $.ajax({
                "url": "/preference/DONT_SHOW_WELCOME/" + ($(el).prop("checked") ? 1 : 0),
                "method": "put",
                "dataType": "json",
                "success": function (response) {
                    console.info('response', response);
                    if (response.status == true) {

                    } else {
                        alertP("Error", "Unable to update preference, please try again later.");
                    }
                },
                "error": function () {
                    alertP("Error", "Unable to update preference, please try again later.");
                }
            })
        }
    </script>
</div>