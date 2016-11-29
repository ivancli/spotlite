@extends('layouts.adminlte')
@section('title', 'Edit Group')
@section('header_title', 'Edit Group')
@section('breadcrumbs')
{{--    {!! Breadcrumbs::render('edit_group', $group) !!}--}}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Group: {{$group->name}}</h3>
                </div>
                <div class="box-body">
                    <div class="um-form-container">
                        @include('um::forms.group.edit')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
