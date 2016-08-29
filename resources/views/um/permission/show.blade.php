@extends('layouts.adminlte')
@section('title', 'Permission Details')
@section('header_title', 'Permission Details')
@section('breadcrumbs')
    {!! Breadcrumbs::render('show_permission', $permission) !!}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Permission: {{$permission->display_name}}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{route('um.permission.edit', $permission->getKey())}}" class="btn btn-box-tool">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="alert alert-info" role="alert">
                        <strong>{{count($permission->roles)}}</strong> {{str_plural('role', count($permission->roles))}}
                        with
                        this
                        permission.
                    </div>
                    <table class="table table-bordered table-hover table-striped">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{$permission->permission_id}}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{$permission->name}}</td>
                        </tr>
                        <tr>
                            <th>Display Name</th>
                            <td>{{$permission->display_name}}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{$permission->description}}</td>
                        </tr>
                        <tr>
                            <th>Parent Permission</th>
                            <td>
                                @if(!is_null($permission->parentPerm))
                                    <a href="{{$permission->parentPerm->urls['show']}}">{{$permission->parentPerm->display_name}}</a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{$permission->created_at}}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{$permission->updated_at}}</td>
                        </tr>
                        <tr>
                            <th>Roles</th>
                            <td>
                                @foreach($permission->roles as $index=>$role)
                                    <a href="{{$role->urls['show']}}">{{$role->display_name}}</a>
                                    @if($index != count($permission->roles)-1)
                                        ,
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop