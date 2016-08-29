@extends('layouts.adminlte')
@section('title', 'Role Details')
@section('header_title', 'Role Details')
@section('breadcrumbs')
    {!! Breadcrumbs::render('show_role', $role) !!}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$role->display_name}}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{route('um.role.edit', $role->getKey())}}" class="btn btn-box-tool">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="alert alert-info" role="alert">
                        <strong>{{count($role->users)}}</strong> {{str_plural('user', count($role->users))}} with this
                        role.
                    </div>
                    <table class="table table-bordered table-hover table-striped">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{$role->getKey()}}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{$role->name}}</td>
                        </tr>
                        <tr>
                            <th>Display Name</th>
                            <td>{{$role->display_name}}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{$role->description}}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{$role->created_at}}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{$role->updated_at}}</td>
                        </tr>
                        <tr>
                            <th>Permissions</th>
                            <td>
                                @foreach($role->perms as $index=>$permission)
                                    <a href="{{$permission->urls['show']}}">{{$permission->display_name}}</a>
                                    @if($index != count($role->perms)-1)
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