@extends('layouts.adminlte')
@section('title', 'My Profile')
@section('header_title', 'My Profile')
@section('breadcrumbs')
    {!! Breadcrumbs::render('profile_index', $user) !!}
@stop
@section('content')
    <div class="row">
        <div class="col-lg-offset-4 col-lg-4 col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$user->first_name}} {{$user->last_name}}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{route('profile.edit')}}" class="btn btn-default btn-sm btn-flat">Edit</a>
                    </div>
                </div>
                <div class="box-body">
                    @include('user.profile.partials.view')
                </div>
            </div>
        </div>
    </div>
@stop