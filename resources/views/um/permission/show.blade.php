@extends('layouts.adminlte')
@section('content')
    <h3>Show Permission</h3>
    <div class="alert alert-info" role="alert">
        <strong>{{count($permission->roles)}}</strong> {{str_plural('role', count($permission->roles))}} with this
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
@stop