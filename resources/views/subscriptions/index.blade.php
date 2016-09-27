@extends('layouts.adminlte')
@section('title', "Manage Subscription")
@section('header_title', "Manage Subscription")
@section('breadcrumbs')
    {!! Breadcrumbs::render('subscription_index') !!}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Current Subscription</h3>

                    <div class="box-tools pull-right">
                        Reference ID: {{$subscription->customer->id}}
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <p>You are currently subscribed to <strong>{{$subscription->product->name}}</strong>
                                package.</p>
                            <p>Next payment will be processed on
                                <strong>{{date('Y-m-d', strtotime($subscription->next_assessment_at))}}</strong>.
                            </p>
                            @if(!is_null($subscription->trial_ended_at))
                                <p>Your trial will be ended on
                                    {{date('Y-m-d', strtotime($subscription->trial_ended_at))}}</p>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            @if(isset($portalLink))
                                <a href="{{$portalLink}}" class="btn btn-default">
                                    Access Chargify Billing Portal
                                </a>
                            @endif
                            <a href="{{$updatePaymentLink}}" target="_blank" class="btn btn-default">
                                Update Payment Details
                            </a>
                            <a href="{{route('subscription.edit', $sub->getKey())}}" class="btn btn-default">
                                Change My Plan
                            </a>
                            {!! Form::model($sub, array('route' => array('subscription.destroy', $sub->getKey()), 'method' => 'delete', 'style'=>'display: inline-block', 'onsubmit'=>'return confirm("Do you want to cancel this subscription package? Please be aware of that this action cannot be undone.")')) !!}
                            {!! Form::submit('Cancel Subscription', ["class"=>"btn btn-default btn-xs"]) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop