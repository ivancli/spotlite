@extends('layouts.adminlte')
@section('title', "Updated Subscription")
@section('header_title', "Updated Subscriptoin")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <h4>Subscription has been updated successfully.</h4>
                    <p>You are now subscribed to <strong>{{$apiSubscription->product->name}}</strong> package.</p>
                    @if(!is_null($apiSubscription->expires_at))
                        <p>Subscription will be expired
                            at {{date('Y-m-d H:i:s', strtotime($apiSubscription->expires_at))}}.</p>
                    @endif
                    <div>
                        <a href="{{route('dashboard.index')}}">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop