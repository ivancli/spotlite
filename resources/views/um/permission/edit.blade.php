@extends('layouts.adminlte')
@section('title', 'Edit Permission')
@section('header_title', 'Edit Permission')
@section('breadcrumbs')
{{--    {!! Breadcrumbs::render('edit_permission', $permission) !!}--}}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Permission: {{$permission->display_name}}</h3>
                </div>
                <div class="box-body">
                    <div class="um-form-container">
                        @include('um::forms.permission.edit')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script type="text/javascript">
        $(function () {
            $(".um-form-container select").select2({
                "allowClear": true,
                "placeholder": ""
            });
        })
    </script>
@stop