@extends('layouts.adminlte')
@section('content')
    <h3>Create Role</h3>
    <div class="um-form-container">
        @include('um::forms.role.create')
    </div>
@stop
@section('scripts')
    <script type="text/javascript">
        $(function () {
            $(".um-form-container select").select2();
        })
    </script>
@stop