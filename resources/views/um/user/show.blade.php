@extends('layouts.adminlte')
@section('title', 'User Details')
@section('header_title', 'User Details')
@section('breadcrumbs')
{{--    {!! Breadcrumbs::render('show_user', $user) !!}--}}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$user->first_name}} {{$user->last_name}}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{route('um.user.edit', $user->getKey())}}" class="btn btn-box-tool">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="alert alert-info" role="alert">
                        <strong>{{count($user->roles)}}</strong> {{str_plural('role', count($user->roles))}} assigned to
                        this
                        user.
                    </div>
                    <table class="table table-bordered table-hover table-striped">
                        <tbody>
                        @foreach($user->toArray() as $field=>$value)
                            @if(!is_array($value))
                                <tr>
                                    <th>
                                        {{$field}}
                                    </th>
                                    <td>
                                        {{dump($value)}}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        <tr>
                            <th>Roles</th>
                            <td>
                                @foreach($user->roles as $index=>$role)
                                    <a href="{{$role->urls['show']}}">{{$role->display_name}}</a>
                                    @if($index!=count($user->roles) - 1)
                                        ,
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>Groups</th>
                            <td>
                                @foreach($user->groups as $index=>$group)
                                    <a href="{{$group->urls['show']}}">{{$group->name}}</a>
                                    @if($index!=count($user->groups) - 1)
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