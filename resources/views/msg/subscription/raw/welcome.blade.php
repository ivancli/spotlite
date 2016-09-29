    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <h4>Thank you for choosing SpotLite</h4>
                    <p>You have subscribed to <strong>{{$apiSubscription->product->name}}</strong> package.</p>
                    @if(!is_null($apiSubscription->expires_at))
                        <p>Subscription will be expired
                            at {{date('Y-m-d H:i:s', strtotime($apiSubscription->expires_at))}}.</p>
                    @endif
                    <div>
                        <a href="{{route('dashboard.index')}}">Back to Dashboard</a>
                    </div>
                </div>
            </div>
            {{--TODO remove debug info--}}
        </div>
    </div>