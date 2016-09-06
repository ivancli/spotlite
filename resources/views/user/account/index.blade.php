@extends('layouts.adminlte')
@section('title', 'Account Settings')
@section('header_title', 'Account Settings')
@section('breadcrumbs')
    {!! Breadcrumbs::render('account_index') !!}
@stop
@section('content')
    <div class="row">
        <div class="col-lg-offset-4 col-lg-4 col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Update Password</h3>
                </div>
                <div class="box-body">
                    <p>By clicking the update password button, an email with update password link will be sent
                        to <a href="mailto:{{$user->email}}">{{$user->email}}</a>.</p>
                    {!! Form::open(array('route' => 'password.post', 'method' => 'post', "id" => "frm-password", 'onsubmit' => 'submitForgotPassword(); return false;')) !!}

                    <input type="hidden" name="email" value="{{$user->email}}">

                    <div class="row">
                        <div class="col-sm-12">
                            {!! Form::submit('Update Password', ["class"=>"btn btn-default btn-sm", "href"=>"#"]) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script type="text/javascript">
        function submitForgotPassword() {
            showLoading();
            $.ajax({
                "url": $("#frm-password").attr("action"),
                "method": "post",
                "data": $("#frm-password").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        alertP('Email Sent', 'An email with reset password link has been sent to provided email address.', function () {
                            window.location.href = "{{route('login.get')}}";
                        });
                    } else {
                        var $errorContainer = $(".errors-container");
                        $errorContainer.empty();
                        $.each(response.errors, function (index, error) {
                            $errorContainer.append(
                                    $("<li>").text(error)
                            );
                        });
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    var $errorContainer = $(".errors-container");
                    $errorContainer.empty();
                    $.each(xhr.responseJSON, function (index, entity) {
                        $.each(entity, function (eIndex, error) {
                            $errorContainer.append(
                                    $("<li>").text(error)
                            );
                        });
                    });
                }
            })
        }

    </script>
@stop