@extends('layouts.adminlte')
@section('title', 'Edit Profile')
@section('header_title', 'Edit Profile')
@section('breadcrumbs')
    {!! Breadcrumbs::render('profile_index') !!}
@stop
@section('content')
    <div class="row">
        {{--<div class="col-sm-12">--}}
        <div class="col-lg-offset-4 col-lg-4 col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$user->first_name}} {{$user->last_name}}</h3>
                </div>
                <div class="box-body">
                    <div class="form-container">
                        @include('user.profile.forms.edit')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop