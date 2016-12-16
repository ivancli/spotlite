<div class="row subscription-info-panel">
    <div class="col-sm-12">
        <div class="row m-b-20">
            <div class="col-sm-12">
                <h4>My Plan</h4>
                <p>{{$subscription->product()->name}}</p>
                @if(!is_null($subscription->trial_ended_at))
                    <p>
                        Trial expiry:
                        {{date(auth()->user()->preference('DATE_FORMAT'), strtotime($subscription->trial_ended_at))}}
                    </p>
                @endif
            </div>
            <div class="col-sm-12">
                <a href="{{route('subscription.edit', $sub->getKey())}}" class="btn btn-primary btn-flat">
                    CHANGE MY PLAN
                </a>
            </div>
        </div>
        <div class="row m-b-20">
            <div class="col-sm-12">
                <h4>Payment Details</h4>
                Upcoming Invoice:
                <strong>{{date(auth()->user()->preference('DATE_FORMAT'), strtotime($subscription->next_assessment_at))}}
                    {{--                    | ${{number_format($subscription->current_billing_amount_in_cents/100, 2)}}--}}
                </strong>
            </div>
        </div>
        @if(isset($transactions))
            <p>Payment History: </p>
            <table class="table table-bordered table-hover table-striped">
                <thead class="thead-inverse">
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Payment</th>
                </tr>
                </thead>
                <tbody>
                @if($transactions->filter(function($value){return $value->transaction_type == "payment";})->count() == 0)
                    <tr>
                        <td colspan="4" align="center">No payment histories in the list</td>
                    </tr>
                @else
                    @foreach($transactions as $transaction)

                        @if($transaction->transaction_type == "payment")
                            <tr>
                                <td>{{date(auth()->user()->preference('DATE_FORMAT'), strtotime($transaction->created_at))}}</td>
                                <td>{{$transaction->memo}}</td>
                                <td>
                                    @if($transaction->kind == "baseline")
                                        Subscription
                                    @elseif($transaction->kind == "initial")
                                        Initial Setup
                                    @endif
                                </td>
                                <td>
                                    ${{number_format($transaction->amount_in_cents/100, 2)}}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endif
                </tbody>
            </table>
        @endif
        <div class="row">
            <div class="col-sm-12 text-right">
                {{--                            @if(is_null($onboardingSubscription) || $onboardingSubscription->product()->initial_charge_in_cents < $onboardingProduct->initial_charge_in_cents)--}}
                {{--@if(is_null($onboardingSubscription))--}}
                {{--<a href="{{route('onboarding.index')}}" class="btn btn-primary btn-flat">--}}
                {{--                                    @if(is_null($onboardingSubscription))--}}
                {{--Purchase Onboarding Service--}}
                {{--@else--}}
                {{--Upgrade Onboarding Service--}}
                {{--@endif--}}
                {{--</a>--}}
                {{--&nbsp;--}}
                {{--@endif--}}
                <a href="{{$updatePaymentLink}}" class="btn btn-primary btn-flat">
                    UPDATE PAYMENT DETAILS
                </a>
                &nbsp;
                <button class="btn btn-default btn-flat"
                        onclick="toggleCancelSubscriptionPanel(); return false;">
                    CANCEL SUBSCRIPTION
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row cancel-subscription-panel">
    <div class="col-sm-12">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h4 class="box-title text-danger"><i class="fa fa-exclamation-triangle"></i> Cancel my Subscription?
                </h4>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        {!! Form::model($sub, array('route' => array('subscription.destroy', $sub->getKey()), 'class'=>'form-horizontal', 'method' => 'delete', 'onsubmit'=>'return confirm("Do you want to cancel this subscription package? Please be aware of that this action cannot be undone.")')) !!}
                        <div class="row">
                            <div class="col-sm-12">
                                <p>We would love to know what made you want to cancel. Please <a href="#">send us your
                                        feedback</a>.</p>
                                <p>
                                    You may choose to keep your profile and settings in SpotLite (recommended) or delete
                                    your profile and settings from SpotLite completely
                                </p>

                                <div class="well">
                                    <h4>Option 1</h4>
                                    <p>I would like to cancel my subscription and keep my profile and settings stored in
                                        SpotLite.</p>
                                    <p>
                                        <small class="text-muted">
                                            You might want to use it again in the future so, by keeping your profile,
                                            you can simply continue using SpotLite once you reactivate your account.
                                        </small>
                                    </p>
                                    <p class="text-danger">
                                        Are you sure you want to cancel your subscription?
                                    </p>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="keep_profile" value="1" checked="">
                                            Yes. I agree to cancel my subscription and keep my profile.
                                        </label>
                                    </div>
                                </div>

                                <div class="well">
                                    <h4>Option 2</h4>
                                    <p>I would like to cancel my subscription and delete my profile and settings from
                                        SpotLite.</p>
                                    <p>
                                        <small class="text-muted">
                                            If you choose to continue using SpotLite in the future, you will need to
                                            enter all your Categories, Products and Product Page URLs once again.
                                        </small>
                                    </p>
                                    <p class="text-danger">
                                        Are you sure you want to cancel your subscription? This action cannot be undone.
                                    </p>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="keep_profile" value="">
                                            Yes. I agree to cancel my subscription and delete my profile.
                                        </label>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-sm-12 text-right">
                                        {!! Form::submit('CONFIRM CANCELLATION', ["class"=>"btn btn-default btn-flat"]) !!}

                                        <button class="btn btn-default btn-flat" id="btn-cancel-cancel"
                                                onclick="toggleCancelSubscriptionPanel();return false;">
                                            CANCEL
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function toggleCancelSubscriptionPanel() {
            var $cancelSubscriptionPanel = $(".cancel-subscription-panel");
            var $subscriptionPanel = $(".subscription-info-panel");
            if ($cancelSubscriptionPanel.is(":visible")) {
                $subscriptionPanel.slideDown();
                $cancelSubscriptionPanel.slideUp();
            } else {
                $subscriptionPanel.slideUp();
                $cancelSubscriptionPanel.slideDown();
            }
        }
    </script>
</div>
