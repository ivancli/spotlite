@extends('layouts.adminlte')
@section('title', 'Forgot Password')
@section('content')
    <div class="row">
        <div class="col-lg-offset-4 col-lg-4 col-md-offset-3 col-md-6 col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Forgot Password</h3>
                </div>
                <div class="box-body">
                    <div class="um-form-container">
                        <ul class="text-danger errors-container">
                        </ul>
                        {!! Form::open(array('route' => 'password.post', 'method' => 'post', "class" => "form-horizontal sl-form-horizontal", "id" => "frm-password", 'onsubmit' => 'submitForgotPassword(); return false;')) !!}

                        <div class="form-group required">
                            {!! Form::label('email', 'Email', array('class' => 'control-label col-md-3')) !!}
                            <div class="col-md-9">
                                {!! Form::email('email', null, array('class' => 'form-control')) !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{route('login.get')}}">Back to login page</a>
                            </div>
                            <div class="col-sm-6 text-right">
                                {!! Form::submit('Forgot', ["class"=>"btn btn-default btn-sm", "href"=>"#"]) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
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