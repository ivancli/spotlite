@extends('layouts.adminlte')
@section('title', 'Account Settings')
@section('breadcrumbs')
    {!! Breadcrumbs::render('account_index') !!}
@stop
@section('content')
    <div class="nav-tabs-custom">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs ui-sortable-handle">
            <li class="active"><a href="#display-settings" data-toggle="tab" aria-expanded="true">Display Settings</a>
            </li>
            <li class=""><a href="#user-settings" data-toggle="tab" aria-expanded="false">Account Settings</a></li>
        </ul>
        <div class="tab-content">
            <!-- Morris chart - Sales -->
            <div class="chart tab-pane active" id="display-settings">
                <div class="row">
                    <div class="col-md-6">
                        <div class="p-10">
                            <h4 class="lead">Date Time</h4>
                            <hr>
                            {!! Form::model(auth()->user()->preferences, array('route' => 'preference.mass_update', 'method' => 'put', 'id' => 'frm-display-settings', 'class' => 'sl-form-horizontal form-horizontal', 'onsubmit' => 'submitDisplaySettings(); return false;')) !!}
                            <div class="form-group">
                                <label for="" class="col-md-3 control-label">Date format</label>
                                <div class="col-md-9">
                                    <select name="preferences[DATE_FORMAT]" id="sel-date-format"
                                            class="form-control sl-form-control">
                                        <option value="Y-m-d" {{auth()->user()->preference('DATE_FORMAT') == 'Y-m-d' ? 'selected': ''}}>{{date('Y-m-d')}}</option>
                                        <option value="d F" {{auth()->user()->preference('DATE_FORMAT') == 'd F' ? 'selected': ''}}>{{date('d F')}}</option>
                                        <option value="l j M Y" {{auth()->user()->preference('DATE_FORMAT') == 'l j M Y' ? 'selected': ''}}>{{date('l j M Y')}}</option>
                                        <option value="l Ymd" {{auth()->user()->preference('DATE_FORMAT') == 'l Ymd' ? 'selected': ''}}>{{date('l Ymd')}}</option>
                                        <option value="l Y-m-d" {{auth()->user()->preference('DATE_FORMAT') == 'l Y-m-d' ? 'selected': ''}}>{{date('l Y-m-d')}}</option>
                                        <option value="l jS \of F Y" {{auth()->user()->preference('DATE_FORMAT') == 'l jS \of F Y' ? 'selected': ''}}>{{date('l jS \of F Y')}}</option>
                                        <option value="l j F Y" {{auth()->user()->preference('DATE_FORMAT') == 'l j F Y' ? 'selected': ''}}>{{date('l j F Y')}}</option>
                                        <option value="l F j, Y" {{auth()->user()->preference('DATE_FORMAT') == 'l F j, Y' ? 'selected': ''}}>{{date('l F j, Y')}}</option>
                                        <option value="l d/m/Y" {{auth()->user()->preference('DATE_FORMAT') == 'l d/m/Y' ? 'selected': ''}}>{{date('l d/m/Y')}}</option>
                                        <option value="l m/d/Y" {{auth()->user()->preference('DATE_FORMAT') == 'l m/d/Y' ? 'selected': ''}}>{{date('l m/d/Y')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-3 control-label">Time format</label>
                                <div class="col-md-9">
                                    <select name="preferences[TIME_FORMAT]" id="sel-time-format"
                                            class="form-control sl-form-control">
                                        <option value="g:i a" {{auth()->user()->preference('TIME_FORMAT') == 'g:i a' ? 'selected' : ''}}>{{date('g:i a')}}</option>
                                        <option value="h:i a" {{auth()->user()->preference('TIME_FORMAT') == 'h:i a' ? 'selected' : ''}}>{{date('h:i a')}}</option>
                                        <option value="g:i A" {{auth()->user()->preference('TIME_FORMAT') == 'g:i A' ? 'selected' : ''}}>{{date('g:i A')}}</option>
                                        <option value="h:i A" {{auth()->user()->preference('TIME_FORMAT') == 'h:i A' ? 'selected' : ''}}>{{date('h:i A')}}</option>
                                        <option value="H:i" {{auth()->user()->preference('TIME_FORMAT') == 'H:i' ? 'selected' : ''}}>{{date('H:i')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                {!! Form::submit('Save', ["class"=>"btn btn-default", "href"=>"#"]) !!}
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="chart tab-pane" id="user-settings">
                <div class="row">
                    <div class="col-md-8">
                        <div class="p-10">
                            <h4 class="lead">User Profile</h4>
                            <hr>
                            @include('user.profile.forms.edit')
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-10">
                            <h4 class="lead">Reset Password</h4>
                            <hr>
                            <p>By clicking the update password button, an email with update password link will be
                                sent
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

        function submitDisplaySettings() {
            showLoading();
            $.ajax({
                "url": $("#frm-display-settings").attr("action"),
                "method": "post",
                "data": $("#frm-display-settings").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        alertP("Display settings", "Display settings has been updated.");
                    } else {
                        var $errorContainer = $("#display-settings .errors-container");
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
                    var $errorContainer = $("#display-settings .errors-container");
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