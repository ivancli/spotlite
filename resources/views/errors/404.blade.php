@extends('layouts.adminlte_no_sidebar')
@section('title', '404 Page not found')
@section('content')
    <div class="row">
        <div class="col-sm-12 text-center">
            <h4>404 The page you are looking for cannot be found.</h4>
            <a href="{{route('dashboard.index')}}">Back to dashboard</a>
        </div>
    </div>
@stop