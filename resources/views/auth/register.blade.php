<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/15/2016
 * Time: 10:54 AM
 */
?>

@extends('layouts.adminlte')

@section('content')
    {!! Form::open(array('url' => 'register', 'method' => 'post', "id" => "frm-register")) !!}
    @include('auth.forms.register_form')
    {!! Form::submit('Register', ["class"=>"btn btn-default btn-sm"]) !!}
    {!! Form::close() !!}
@stop
