@extends('layouts.adminlte')
@section('title', 'Member Login')
@section('content')
    <div class="row">
        <div class="col-sm-6">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Member login</h3>
                </div>
                <div class="box-body">
                    <div class="um-form-container">
                        @if(isset($errors))
                            <ul class="text-danger">
                                @foreach ($errors->all('<li>:message</li>') as $message)
                                    {!! $message !!}
                                @endforeach
                            </ul>
                        @endif
                        {!! Form::open(array('route' => 'login.post', 'method' => 'post', "id" => "frm-login")) !!}
                        @include('auth.forms.login_form')
                            <div class="row m-b-5">
                                <div class="col-sm-6">
                                    <a href="{{route('register.get')}}">Not a member yet? Click here to register.</a>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <a href="#">Forgot password?</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    {!! Form::submit('Login', ["class"=>"btn btn-default btn-sm"]) !!}
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
