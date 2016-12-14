@extends('layouts.adminlte')
@section('title', 'Onboarding Service')
@section('header_title', $onboardingProduct->name)
@section('breadcrumbs')
{{--    {!! Breadcrumbs::render("onboarding_index") !!}--}}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    @if(!is_null($onboardingSubscription))
                        <div class="row">
                            <div class="col-sm-12">
                                <p>
                                    You are currently on <strong>{{$onboardingSubscription->product()->name}}</strong>.
                                </p>
                                @if(!is_null($onboardingSubscription) && $onboardingSubscription->product()->initial_charge_in_cents >= $onboardingProduct->initial_charge_in_cents)
                                    <p>Please contact support if you need help on onboarding service</p>
                                @endif
                            </div>
                        </div>
                        <hr>
                    @endif
{{--                    @if(is_null($onboardingSubscription) || $onboardingSubscription->product()->initial_charge_in_cents < $onboardingProduct->initial_charge_in_cents)--}}
                    @if(is_null($onboardingSubscription))
                        <div class="row">
                            <div class="col-md-8">
                                <p>Onboarding Service includes the following activities:</p>
                                <p>
                                    {!! $onboardingProduct->description !!}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>Product Information</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered">
                                            <tbody>
                                            <tr>
                                                <th>Name</th>
                                                <td class="text-center">{{$onboardingProduct->name}}</td>
                                            </tr>
                                            <tr>
                                                <th>Price</th>
                                                <td class="text-center">
                                                    ${{number_format($previewSubscription->current_billing_manifest->total_in_cents / 100, 2, '.', ',')}}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 text-right">
                                        <button class="btn btn-primary btn-flat"
                                                onclick="purchaseOnboardingService(); return false;">
                                            @if(is_null($onboardingSubscription))
                                                Purchase Now
                                            @elseif($onboardingSubscription->product()->initial_charge_in_cents < $onboardingProduct->initial_charge_in_cents)
                                                Upgrade Now
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        function purchaseOnboardingService() {
            showLoading();
            $.ajax({
                "url": "{{route('onboarding.store')}}",
                "method": "post",
                "dataType": "json",
                "success": function (response) {
                    if (response.status == true) {
                        window.location.href = "{{route('subscription.index')}}";
                    } else {
                        alertP("Oops! Something went wrong.", "Unable to purchase {{$onboardingProduct->name}}, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }
    </script>
@stop