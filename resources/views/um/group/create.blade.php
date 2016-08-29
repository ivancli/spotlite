@extends('layouts.adminlte')
@section('title', 'Create Group')
@section('header_title', 'Create Group')
@section('breadcrumbs')
    {!! Breadcrumbs::render('create_group') !!}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="um-form-container">
                        @include('um::forms.group.create')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop