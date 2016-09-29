@extends('layouts.adminlte')
@section('title', 'Reports')
@section('header_title', "Reports")

@section('breadcrumbs')
    {!! Breadcrumbs::render('report_index') !!}
@stop

@section('content')
    {{--@include('products.partials.banner_stats')--}}
    <div class="row">
        <div class="col-md-8 col-sm-7">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Scheduled Report</h3>
                </div>
                <div class="box-body">
                    <div class="row m-b-10">
                        <div class="col-sm-12">
                            {{--TODO put real content here--}}
                            Whatever it is
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-5">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Historical Reports</h3>
                </div>
                <div class="box-body">
                    <div class="row m-b-10">
                        <div class="col-sm-12">
                            {{--TODO put real content here--}}
                            Historical reports here
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop