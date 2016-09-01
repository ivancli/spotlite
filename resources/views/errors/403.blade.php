@extends('layouts.adminlte_no_sidebar')
@section('title', 'Be right back')
@section('content')
    <div class="row">
        <div class="col-sm-12 text-center">
            <h4>403 Unauthorised access.</h4>
            <a href="{{route('dashboard.index')}}">Back to dashboard</a>
        </div>
    </div>
@stop