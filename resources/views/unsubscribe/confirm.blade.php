@extends('layouts.adminlte_auth')
@section('title', 'Account Login')
@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{route('dashboard.index')}}">
                <img src="{{asset('build/images/logo.png')}}" alt="" width="250">
            </a>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Are you sure you want to unsubscribe the following email address?</p>
            <p class="login-box-msg"><strong>{{$email}}</strong></p>
            <p class="login-box-msg">Please note that by unsubscribing, you will not receive Reset Password, Alert
                and Report emails from SpotLite.</p>
            {!! Form::open(array('route' => 'unsubscribe.store', 'method' => 'post', "id" => "frm-unsubscribe")) !!}
            <input type="hidden" name="email" value="{{$email}}">
            <input type="hidden" name="page" value="{{$page}}">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button type="submit" class="btn btn-primary btn-flat">Confirm</button>
                    <a href="{{route('dashboard.index')}}" class="btn btn-default btn flat">Cancel</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop