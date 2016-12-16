<div class="modal fade" tabindex="-1" role="dialog" id="modal-site-prices">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{parse_url($siteURL)['host']}}</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                <div class="prices-container">
                    <p>Below are the prices detected for the provided URL. Please select a correct price: </p>
                    @if(isset($targetDomain))
                        <div class="radio">
                            <label>
                                <input type="radio" class="rad-site-id" value="{{$targetDomain['domain_id']}}"
                                       name="domain_id"
                                       onclick="$('.rad-site-id[name=site_id]').prop('checked', false);">
                                <input type="hidden" value="{{$targetDomain['recent_price']}}" name="domain_price">
                                <span>${{number_format($targetDomain['recent_price'], 2, '.', ',')}}</span>
                            </label>
                        </div>
                    @endif
                    @foreach($sites as $site)
                        <div class="radio">
                            <label>
                                <input type="radio" value="{{$site->getKey()}}" name="site_id"
                                       onclick="$('.rad-site-id[name=domain_id]')e.prop('checked', false);"
                                       class="rad-site-id">
                                <span>${{number_format($site->recent_price, 2, '.', ',')}}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="error-panel" style="display: none;">
                    <p>Please help us to locate the price from the Product URL: </p>
                    <textarea name="comment" id="txt-comment-site-error" class="form-control" rows="5"
                              style="resize: vertical;"
                              placeholder="e.g. the correct price should be $121.40"></textarea>
                </div>
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-set-price">OK</button>
                <button class="btn btn-warning btn-flat" id="btn-error" onclick="showErrorPanel(this);return false;">
                    INCORRECT PRICE
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(params) {
            $("#btn-set-price").on("click", function () {
                var valid = true;
                if ($(".rad-site-id:checked").length == 0) {
                    if ($(".error-panel").is(":visible")) {
                        if ($("#txt-comment-site-error").val().trim() == "") {
                            alertP("Oops! Something went wrong.", "Please help us to locate the correct price.");
                            return false;
                        }
                    } else {
                        alertP("Oops! Something went wrong.", "Please select a price from the list. Alternatively, help us to locate the correct price by clicking 'INCORRECT PRICE' button.");
                        return false;
                    }
                }

                if ($.isFunction(params.callback)) {
                    var callbackData = [];
                    callbackData[$(".rad-site-id:checked").attr("name")] = $(".rad-site-id:checked").val();
                    callbackData["domain_price"] = $(".rad-site-id:checked").attr("name") == "domain_id" ? $(".rad-site-id:checked").siblings('span') : null;
                    callbackData["comment"] = $("#txt-comment-site-error").val();
                    params.callback(callbackData);
                }
                $("#modal-site-prices").modal("hide");
            });
        }

        function showErrorPanel(el) {
            $(el).hide();
            $(".error-panel").slideDown();
            $(".prices-container").slideUp();
            $(".rad-site-id").prop("checked", false);
        }
    </script>
</div>
