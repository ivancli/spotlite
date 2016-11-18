<div class="modal fade" tabindex="-1" role="dialog" id="modal-crawler-update">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Update Crawler</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::model($crawler, array('route' => array('admin.crawler.update', $crawler->getKey()), 'method'=>'put', "onsubmit"=>"return false", "id"=>"frm-crawler-update")) !!}
                <div class="form-group">
                    {!! Form::label('crawler_class', 'Crawler Class', array('class' => 'control-label')) !!}
                    {!! Form::text('crawler_class', null, array('class' => 'form-control', 'id'=>'txt-parser-class', 'placeholder'=>'DefaultCrawler')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('parser_class', 'Parser Class', array('class' => 'control-label')) !!}
                    {!! Form::text('parser_class', null, array('class' => 'form-control', 'id'=>'txt-parser-class', 'placeholder'=>'XPathParser')) !!}
                </div>
                {!! Form::close() !!}

            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-update-crawler">OK</button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#btn-update-crawler").on("click", function () {
                submitUpdateCrawler(function (response) {
                    if (response.status == true) {
                        $("#modal-crawler-update").modal("hide");
                    } else {
                        alertP("Error", "Unable to update crawler, please try again later.");
                    }
                }, function (xhr, status, error) {
                    alertP("Error", "Unable to update crawler, please try again later.");
                })
            });
        }

        function submitUpdateCrawler(successCallback, failCallback) {
            showLoading();
            $.ajax({
                "url": $("#frm-crawler-update").attr("action"),
                "method": "put",
                "data": $("#frm-crawler-update").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if ($.isFunction(successCallback)) {
                        successCallback(response);
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            })
        }
    </script>
</div>
