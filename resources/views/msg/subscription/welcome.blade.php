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
            <div class="m-b-5">
                <iframe width="100%" height="300" src="https://www.youtube.com/embed/vUF7ja9ehIs" frameborder="0"
                        allowfullscreen></iframe>
            </div>

            {{--<div class="row">--}}
            {{--<div class="col-sm-12 text-center">--}}
            {{--<div class="checkbox">--}}
            {{--<label>--}}
            {{--<input type="checkbox" onclick="updateDontShowWelcomePage(this)"> Don't show again--}}
            {{--</label>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<p class="text-center" style="padding: 20px">--}}
            {{--<button class="btn btn-default" title="Close this popup" data-dismiss="modal">No, thanks--}}
            {{--</button>--}}
            {{--<button class="btn btn-success" title="Close this popup and start the tour" id="start_tour"--}}
            {{--data-dismiss="modal" onclick="return false;">Start the tour--}}
            {{--</button>--}}
            {{--</p>--}}

            <div class="row">
                <div class="col-sm-4 text-center">
                    <div>Download the tutorial</div>
                    <div>
                        <a href="#" class="text-muted">
                            <div style="font-size: 25px;">
                                <i class=" fa fa-download"></i>
                            </div>
                            <div class="text-success">GET IT NOW</div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-4"></div>
                <div class="col-sm-4"></div>
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