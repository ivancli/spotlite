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
                                       onclick="$('.rad-site-id[name=domain_id]').prop('checked', false);"
                                       class="rad-site-id">
                                <span>${{number_format($site->recent_price, 2, '.', ',')}}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                {!! Form::close() !!}

            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-set-price">OK</button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(params) {
            $("#btn-set-price").on("click", function () {
                if ($(".rad-site-id:checked").length == 0) {
                    alertP("Error", "Please select a price from the list.");
                } else {
                    if ($.isFunction(params.callback)) {
                        var callbackData = [];
                        callbackData[$(".rad-site-id:checked").attr("name")] = $(".rad-site-id:checked").val();
                        params.callback(callbackData);
                    }
                    $("#modal-site-prices").modal("hide");
                }
            });
        }
    </script>
</div>
