@extends('layouts.adminlte')
@section('title', 'Dashboard')
@section('header_title', 'Dashboard')
@section('breadcrumbs')
    {!! Breadcrumbs::render('home') !!}
@stop
@section('content')
    Blank page


    <div class="coll-site-preloader">
        <div class="coll-preloader-container">
            <div class="spinner">
                <div class="dot1"></div>
                <div class="dot2"></div>
            </div>
        </div>
    </div>

@stop