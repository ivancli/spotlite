@extends('layouts.adminlte')
@section('title', 'Edit Group')
@section('header_title', 'Edit Group')
@section('breadcrumbs')
{{--    {!! Breadcrumbs::render('group_edit', $group) !!}--}}
@stop
@section('content')
    <div class="row">
        <div class="col-lg-offset-4 col-lg-4 col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Group</h3>
                </div>
                <div class="box-body">
                    @include('user.group.forms.edit')
                </div>
            </div>
        </div>
    </div>
@stop