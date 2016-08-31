@extends('layouts.adminlte')
@section('title', "Welcome to SpotLite")
@section('header_title', "Welcome to SpotLite")
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Current Subscription</h3>

                    <div class="box-tools pull-right">
                        <a href="{{route('um.group.create')}}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </div>
                <div class="box-body">
                    {{dump($allSubs)}}
                    {{dump($subscription)}}
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <button class="btn btn-primary btn-lg">Upgrade</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop