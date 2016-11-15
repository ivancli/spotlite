@extends('layouts.adminlte')
@section('title', 'Reset Password')
@section('content')
    <div class="row">
        <div class="col-sm-6">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Reset Password</h3>
                </div>
                <div class="box-body">
                    <div class="um-form-container">
                        <ul class="text-danger errors-container">
                        </ul>
                        {!! Form::open(array('route' => 'password.reset.post', 'method' => 'post', "id" => "frm-reset", 'onsubmit'=>'submitUpdatePassword(); return false;')) !!}
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group required">
                            {!! Form::label('email', 'Email', array('class' => 'control-label')) !!}
                            {!! Form::email('email', $email, array('class' => 'form-control', 'readonly'=>'readonly')) !!}
                        </div>

                        <div class="form-group required">
                            {!! Form::label('password', 'Password', array('class' => 'control-label')) !!}
                            {!! Form::password('password', array('class' => 'form-control')) !!}
                        </div>
                        <div class="form-group required">
                            {!! Form::label('password_confirmation', 'Confirm password', array('class' => 'control-label')) !!}
                            {!! Form::password('password_confirmation', array('class' => 'form-control')) !!}
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                {!! Form::submit('Reset', ["class"=>"btn btn-default btn-sm btn-flat", "href"=>"#"]) !!}
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
        function submitUpdatePassword() {
            showLoading();
            $.ajax({
                "url": $("#frm-reset").attr("action"),
                "method": "post",
                "data": $("#frm-reset").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        alertP('Reset Password', 'Your password has been updated.', function () {
                            window.location.href = "{{route('dashboard.index')}}";
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