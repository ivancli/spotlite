<div class="row subscription-info-panel">
    <div class="col-sm-12">
        <div class="row m-b-20">
            <div class="col-sm-12">
                You are currently subscribed to <strong>{{$subscription->product()->name}}</strong>
                @if(!is_null($onboardingSubscription))
                    and {{$onboardingSubscription->product()->name}}
                @endif
                <div class="box-tools pull-right">
                    Reference ID: {{$subscription->customer_id}}
                </div>
            </div>
        </div>
        <div class="row m-b-20">
            <div class="col-sm-12">
                @if(!is_null($subscription->trial_ended_at))
                    Trial:
                    <strong>{{date(auth()->user()->preference('DATE_FORMAT'), strtotime($subscription->trial_started_at))}}
                        to {{date(auth()->user()->preference('DATE_FORMAT'), strtotime($subscription->trial_ended_at))}}</strong>
                @endif
            </div>
        </div>
        <div class="row m-b-20">
            <div class="col-sm-12">
                Upcoming Invoice:
                <strong>{{date(auth()->user()->preference('DATE_FORMAT'), strtotime($subscription->next_assessment_at))}}
                    | ${{number_format($subscription->current_billing_amount_in_cents/100, 2)}}</strong>
            </div>
        </div>
        @if(isset($transactions))
            <h4>Payment History</h4>
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
                <a href="{{$updatePaymentLink}}" class="btn btn-default btn-flat">
                    Update Payment Details
                </a>
                &nbsp;
                <a href="{{route('subscription.edit', $sub->getKey())}}" class="btn btn-default btn-flat">
                    Change My Plan
                </a>
                &nbsp;
                <button class="btn btn-default btn-flat"
                        onclick="toggleCancelSubscriptionPanel(); return false;">
                    Cancel Subscription
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
                                    You may choose to keep your profile and settings in SpotLite (recommended) or delete your profile and settings from SpotLite completely
                                </p>

                                <div class="well">
                                    <p class="text-danger">
                                        <small>[Camila and Michael to work on terms and conditions, the words below
                                            are only an example]
                                        </small>
                                    </p>
                                    <p>Would you like to keep your profile in SpotLite just so you don't need to
                                        sign up
                                        again when you change your mind.</p>
                                    <p class="text-danger">
                                        <small>[description here to indicate what we keep in SpotLite (or maybe
                                            Chargify?]
                                        </small>
                                    </p>
                                    <p class="text-danger">
                                        <small>[description should also mention about the drawback of removing
                                            profile]
                                        </small>
                                    </p>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="keep_profile" value="1"> Yes, I agree to
                                            keep my profile in SpotLite
                                            <small class="text-danger">(and Chargify?)</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 text-right">
                                        <button class="btn btn-default btn-flat" id="btn-cancel-cancel"
                                                onclick="toggleCancelSubscriptionPanel();return false;">
                                            No, go back
                                        </button>

                                        {!! Form::submit('Yes, cancel my subscription', ["class"=>"btn btn-default btn-flat"]) !!}
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
