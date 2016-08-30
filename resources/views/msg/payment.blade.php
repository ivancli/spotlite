@extends('layouts.adminlte')
@section('title', $title)
@section('header_title', $title)
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <h3>{{$bodyTitle}}</h3>
                    <p>{!! $bodyContent !!}</p>
                    <div>
                        <a href="{{route('dashboard.index')}}">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop