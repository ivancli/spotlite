@extends('layouts.adminlte')
@section('title', 'User Activity Logs')
@section('header_title', 'User Activity Logs')
@section('breadcrumbs')
    {!! Breadcrumbs::render('user_activity_log') !!}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table id="tbl-log" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th class="shrink">ID</th>
                            <th>User</th>
                            <th>Activity</th>
                            <th>Date time</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop