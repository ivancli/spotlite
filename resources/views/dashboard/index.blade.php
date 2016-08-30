@extends('layouts.adminlte')
@section('title', 'Dashboard')
@section('header_title', 'Dashboard')
@section('breadcrumbs')
    {!! Breadcrumbs::render('home') !!}
@stop
@section('content')
    Blank page

    @if(isset($login) && $login === true)
        Yo, u just logged in.
    @endif
@stop