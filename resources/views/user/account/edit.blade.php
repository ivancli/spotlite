@extends('layouts.adminlte')
@section('title', 'Account Settings')
@section('header_title', 'Account Settings')
@section('breadcrumbs')
{{--    {!! Breadcrumbs::render('account_index') !!}--}}
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
                    <div class="form-container">

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script type="text/javascript">
        $(function () {
            $(".form-container select").select2({
                "allowClear": true,
                "placeholder": ""
            });
        })
    </script>
@stop