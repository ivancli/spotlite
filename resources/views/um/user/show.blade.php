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
                        <tr>
                            <th>Categories</th>
                            <td>
                                <p>Number of categories: {{$user->categories()->count()}}</p>
                                <p>
                                    <strong>
                                        @foreach($user->categories as $index=>$category)
                                            {{$category->category_name}}
                                            @if($index!=$user->categories()->count() - 1)
                                                ,
                                            @endif
                                        @endforeach
                                    </strong>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th>Products</th>
                            <td>
                                <p>Number of products: {{$user->products()->count()}}</p>
                                <p>
                                    <strong>
                                        @foreach($user->products as $index=>$product)
                                            {{$product->product_name}}
                                            @if($index!=$user->products()->count() - 1)
                                                ,
                                            @endif
                                        @endforeach
                                    </strong>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th>Product Page URLs</th>
                            <td>
                                <p>Number of product page URLs: {{$user->sites()->count()}}</p>
                                <p>
                                    @foreach($user->sites as $index=>$site)
                                        <a target="_blank" href="{{$site->site_url}}">{{$site->site_url}}</a>
                                        @if($index!=$user->sites()->count() - 1)
                                            <br>
                                        @endif
                                    @endforeach
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop