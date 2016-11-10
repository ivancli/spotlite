<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <!--<h4 class="modal-title" id="myModalLabel">Welcome to Composer!</h4>-->
    <div class="row">
        <div class="col-sm-12">
            <h3 class="text-center">
                <img src="{{asset('images/logo-fixed-2.png')}}" style="width: 30%;">
            </h3>
            <h2 class="text-center">
                Hi {{auth()->user()->first_name}}, Welcome to SpotLite
            </h2>
            <p class="text-center">
                The way you make your pricing decisions is about to change.
            </p>
            <p class="text-center">
                Here are a few handy guides you might find useful to get you quickly set up!
            </p>
            <div class="m-b-10 tutorial-video" style="display: none;">
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
                            <td width="25%" style="vertical-align: bottom;" align="center">
                                <div class="hidden-xs">Watch our video tutorial</div>
                                <div>
                                    <a href="#" class="text-muted" style="font-size: 50px; color: #ec0000;"
                                       onclick="showTutorialVideo()">
                                        <div>
                                            <i class="fa fa-youtube-play"></i>
                                        </div>
                                    </a>
                                </div>
                            </td>
                            <td width="25%" style="vertical-align: bottom;" align="center">
                                <div class="hidden-xs">Download our step-by-step guide</div>
                                <div>
                                    <a href="{{asset('videos/sample_video.mp4')}}" class="text-muted">
                                        <div style="font-size: 50px; color: #000056;">
                                            <i class=" fa fa-download"></i>
                                        </div>
                                    </a>
                                </div>
                            </td>
                            <td width="25%" style="vertical-align: bottom;" align="center">
                                <div class="hidden-xs">Check out the FAQ</div>
                                <div>
                                    <a href="#" class="text-muted" style="color: #005100;">
                                        <div style="font-size: 50px;">
                                            <i class=" fa fa-question-circle-o"></i>
                                        </div>
                                    </a>
                                </div>
                            </td>
                            <td width="25%" style="vertical-align: bottom;" align="center">
                                <div class="hidden-xs">Just get started!</div>
                                <div>
                                    <a href="{{route('dashboard.index')}}" class="text-muted">
                                        <div style="font-size: 50px;">
                                            <img src="{{asset('images/favicon.png')}}" alt="" width="50">
                                        </div>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center">
                                <div>If you have any questions or concerns, don't hesitate to get in touch!</div>
                                <div class="text-center">
                                    <a href="mailto:admin@spotlite.com.au" style="font-size: 50px; color: #b97600">
                                        <i class="fa fa-envelope"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" onclick="updateDontShowWelcomePage(this)">
                                        Do not show this window again
                                    </label>
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

        function showTutorialVideo() {
            $(".tutorial-video").slideDown();
        }
    </script>
</div>