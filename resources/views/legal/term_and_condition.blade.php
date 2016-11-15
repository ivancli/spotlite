<div class="modal fade" tabindex="-1" role="dialog" id="modal-site-store">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{$product->product_name}}</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::open(array('route' => array('site.store'), 'method'=>'post', "onsubmit"=>"return false", "id"=>"frm-site-store")) !!}
                <input type="hidden" name="product_id" value="{{$product->getKey()}}">
                <div class="form-group required">
                    {!! Form::label('site_url', 'URL', array('class' => 'control-label')) !!}
                    &nbsp;
                    <a href="#" class="text-muted" data-toggle="popover" style="font-size: 16px; font-weight: bold;"
                       data-placement="right" onclick="return false;" data-trigger="hover"
                       data-content="Add the URL for the product you wish to track by going to the product's webpage, copying the URL from the address bar of your browser and pasting it in this field.">
                        <i class="fa fa-question-circle"></i>
                    </a>
                    {!! Form::text('site_url', null, array('class' => 'form-control m-b-5', 'id'=>'txt-site-url', 'placeholder' => 'Enter or copy URL')) !!}
                </div>
                <div class="prices-container" style="display: none;">
                    <p>Please select a correct price from below: </p>
                </div>

                <div class="report-error-container" style="display: none;">
                </div>

                {!! Form::close() !!}

            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" id="btn-agree" style="display: none;">I Agree</button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
        }
    </script>
</div>
