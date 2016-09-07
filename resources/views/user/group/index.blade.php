@extends('layouts.adminlte')
@section('title', 'My Groups')
@section('header_title', 'My Groups')
@section('breadcrumbs')
    {!! Breadcrumbs::render('group', $user) !!}
@stop
@section('content')
    <div class="row">
        <div class="col-lg-offset-4 col-lg-4 col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Associated group:</h3>
                </div>
                <div class="box-body">
                    @if($groups->count() > 0)
                        <table class="table table-bordered table-hover table-striped" id="tbl-groups">
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
                                        &nbsp;

                                        {!! Form::model($group, array('route' => array('group.destroy', $group->getKey()), 'method'=>'delete', 'onsubmit'=>'return false;', 'style'=>'display:inline-block')) !!}
                                        <a href="#" class="text-danger"
                                           onclick="deleteGroupOnClick(this)">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        {!! Form::close() !!}
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

@section('scripts')
    <script type="text/javascript">
        function deleteGroupOnClick(el) {
            confirmP("Delete Group", "Do you want to delete this group?", {
                "affirmative": {
                    "callback": function () {
                        showLoading();
                        var $form = $(el).closest("form");
                        $.ajax({
                            "url": $form.attr("action"),
                            "method": "delete",
                            "data": $form.serialize(),
                            "dataType": "json",
                            "success": function (response) {
                                hideLoading();
                                if (response.status == true) {
                                    alertP("Delete Group", "The group has been deleted.");
                                    $form.closest("tr").remove();
                                    if ($("#tbl-groups tbody tr").length == 0) {
                                        $("#tbl-groups").replaceWith(
                                                $("<div>").append(
                                                        $("<p>").addClass("text-center").append(
                                                                "No groups available, ",
                                                                $("<a>").attr({
                                                                    "href": "{{route('group.create')}}"
                                                                }).text("click here to add a group"),
                                                                "."
                                                        )
                                                ).html()
                                        )
                                    }


                                } else {
                                    alertP("Error", "Unable to delete group, please try again later.");
                                }
                            },
                            "error": function (xhr, status, errors) {
                                hideLoading();
                                alertP("Error", "Unable to delete group, please try agian later.");
                            }
                        })
                    },
                    "dismiss": true,
                    "class": "btn-danger"
                },
                "negative": {
                    "dismiss": true,
                    "class": "btn-default"
                }
            });

        }
    </script>
@stop