@extends('layouts.adminlte')
@section('title', 'Create Group')
@section('header_title', 'Create Group')
@section('breadcrumbs')
    {!! Breadcrumbs::render('group_create') !!}
@stop
@section('content')
    <div class="row">
        <div class="col-lg-offset-4 col-lg-4 col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Create New Group</h3>
                </div>
                <div class="box-body">
                    @include('user.group.forms.create')
                </div>
            </div>
        </div>
    </div>
@stop