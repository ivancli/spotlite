<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
    <!--<h4 class="modal-title" id="myModalLabel">Welcome to Composer!</h4>-->
    <div class="row">
        <div class="col-sm-12">
            <p class="text-center">
                <img src="{{asset('build/images/logo-fixed-2.png')}}" style="width: 40%; padding-bottom: 20px">
            </p>

            <h3 class="text-center">
                Your credit card is expiring soon
            </h3>

            <p class="text-center" style="padding: 20px 60px 20px 60px">
                Your credit card will be expired soon, please update your credit card details.
            </p>
            <p class="text-center" style="padding: 20px">
                <button class="btn btn-default btn-flat" title="Close this popup" data-dismiss="modal">No, thanks
                </button>
                <a class="btn btn-success btn-flat" href="{{$updatePaymentLink}}">
                    Update Payment Details
                </a>
            </p>
        </div>
    </div>
</div>