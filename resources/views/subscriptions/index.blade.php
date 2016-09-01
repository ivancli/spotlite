@extends('layouts.adminlte')
@section('title', "Welcome to SpotLite")
@section('header_title', "Welcome to SpotLite")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Current Subscription</h3>

                    <div class="box-tools pull-right">
                        {!! Form::model($sub, array('route' => array('subscription.destroy', $sub->getKey()), 'method' => 'delete', 'onsubmit'=>'return confirm("Do you want to cancel this subscription package? Please be aware of that this action cannot be undone.")')) !!}
                        {!! Form::submit('Cancel Subscription', ["class"=>"btn btn-danger btn-sm"]) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <p>You are currently subscribed to <strong>{{$subscription->product->name}}</strong>
                                package.</p>
                            <p>Next payment will be processed at
                                <strong>{{date('Y-m-d H:i:s', strtotime($subscription->next_assessment_at))}}</strong>.
                            </p>
                        </div>
                    </div>
                    {{dump($allSubs)}}
                    {{dump($subscription)}}
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <a href="{{route('subscription.edit', $sub->getKey())}}" class="btn btn-primary">
                                Change My Plan
                            </a>
                            {{--<button class="btn btn-default">Update Payment Method</button>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop