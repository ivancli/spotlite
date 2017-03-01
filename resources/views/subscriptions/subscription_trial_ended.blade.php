@extends('layouts.adminlte')
@section('title', 'Subscription')
@section('header_title', "Trial Ended")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row m-b-10">
                <div class="col-sm-12 text-center">
                    <p>
                        Your subscription has expired. We have sent an email to you tp update payment details.
                    </p>
                    <p>
                        Alternatively you can <a href="{{$updatePaymentLink}}">click here to update your payment details</a> and subscription will be reactivated automatically.
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop