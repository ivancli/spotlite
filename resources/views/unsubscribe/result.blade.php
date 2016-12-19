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
            <p class="login-box-msg">
                You have successfully unsubscribed <strong>{{$email}}</strong>. This email address will not receive
                Reset Password, Alert and Report emails from SpotLite.
            </p>
            <p class="login-box-msg text-muted">
                <a href="{{route('dashboard.index')}}">Back</a>
            </p>
        </div>
    </div>
@stop