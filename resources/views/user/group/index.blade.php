@extends('layouts.adminlte')
@section('title', 'My Profile')
@section('header_title', 'My Profile')
@section('breadcrumbs')
    {!! Breadcrumbs::render('profile_index', $user) !!}
@stop
@section('content')
    <div class="row">
        <div class="col-lg-offset-4 col-lg-4 col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Associated groups:</h3>
                </div>
                <div class="box-body">
                    @if($groups->count() > 0)

                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                            <tr>
                                <th>Group name</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($groups as $group)
                                <tr>
                                    <td>{{$group->name}}</td>
                                    <td class="text-center">
                                        <a href="{{route('group.edit', $group->getKey())}}">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="#">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center">
                            No groups available, <a href="{{route('group.create')}}">click here to add a group</a>.
                        </p>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop