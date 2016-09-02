@extends('layouts.adminlte')
@section('title', 'Profile')
@section('header_title', 'Profile')
@section('breadcrumbs')
    {!! Breadcrumbs::render('profile_show', $user) !!}
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
                    <table class="table table-bordered table-hover table-striped">
                        <tbody>
                        <tr>
                            <th>First name</th>
                            <td>{{$user->first_name}}</td>
                        </tr>
                        <tr>
                            <th>Last name</th>
                            <td>{{$user->last_name}}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{$user->email}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop