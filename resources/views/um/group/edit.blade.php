@extends('layouts.adminlte')
@section('content')
    <h3>Edit Group: {{$group->name}}</h3>
    @include('um::forms.group.edit')
@stop
