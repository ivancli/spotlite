@extends('layouts.adminlte')
@section('title', 'Account Login')
@section('content')
    <div class="row">
        <div class="col-md-offset-3 col-md-6 col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Account login</h3>

                    <div class="box-tools pull-right">
                        <a class="btn btn-default btn-sm" href="{{route('register.get')}}">Don't have an account? Sign Up Now</a>
                    </div>
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
                        {!! Form::open(array('route' => 'login.post', 'method' => 'post', "id" => "frm-login", "class"=>"form-horizontal sl-form-horizontal")) !!}
                        @include('auth.forms.login_form')
                        <div class="row m-b-5">
                            <div class="col-sm-6 text-left">
                                <a href="{{route('password.get')}}">Forgot password?</a>
                            </div>
                            <div class="col-sm-6 text-right">
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
