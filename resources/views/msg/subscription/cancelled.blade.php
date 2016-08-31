@extends('layouts.adminlte')
@section('title', "Cancelled Subscription")
@section('header_title', "Sorry to see you leaving")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <h4>Subscription has been cancelled</h4>
                    <p>You have cancelled the subscription of <strong>{{$apiSubscription->product->name}}</strong>
                        package.</p>
                    {{-- TODO --}}
                    <h4>Provide a survey to user.</h4>
                </div>
            </div>
        </div>
    </div>
@stop