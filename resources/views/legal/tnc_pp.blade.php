<div class="modal fade" tabindex="-1" role="dialog" id="modal-site-store">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Terms</h4>
            </div>
            <div class="modal-body">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="panel-term-and-condition">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                                   href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Terms and Conditions
                                </a>
                            </h4>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel"
                             aria-labelledby="headingThree">
                            <div class="panel-body term-and-condition-panel-body">
                                Loading...
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="panel-privacy-policy">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                                   href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Privacy Policy
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                             aria-labelledby="headingTwo">
                            <div class="panel-body privacy-policy-panel-body">
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-agree">I Agree</button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            getActivePrivacyPolicy(function (response) {
                if (response.status == true) {
                    $(".privacy-policy-panel-body").html(response.pp.content);
                }
            });
            getActiveTermAndCondition(function (response) {
                if (response.status == true) {
                    $(".term-and-condition-panel-body").html(response.tnc.content);
                }
            });
            $("#btn-agree").on("click", function(){
                if($.isFunction(options.callback)){
                    options.callback();
                }
                $(this).closest(".modal").modal("hide");
            })

        }

        function getActivePrivacyPolicy(callback) {
            $.ajax({
                "url": '{{route('privacy_policy.show', 0)}}',
                "method": 'get',
                "dataType": 'json',
                "success": function (response) {
                    console.info('response', response);
                    if ($.isFunction(callback)) {
                        callback(response);
                    }
                },
                "error": function (xhr, status, error) {
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function getActiveTermAndCondition(callback) {
            $.ajax({
                "url": '{{route('term_and_condition.show', 0)}}',
                "method": 'get',
                "dataType": 'json',
                "success": function (response) {
                    console.info('response', response);
                    if ($.isFunction(callback)) {
                        callback(response);
                    }
                },
                "error": function (xhr, status, error) {
                    describeServerRespondedError(xhr.status);
                }
            })
        }
    </script>
</div>
