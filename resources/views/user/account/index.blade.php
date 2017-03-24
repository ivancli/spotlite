@extends('layouts.adminlte')
@section('title', 'Account Settings')

@section('head_scripts')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@stop

@section('header_title', "Account Settings")

@section('breadcrumbs')
    {{--    {!! Breadcrumbs::render('account_index') !!}--}}
@stop
@section('content')
    <hr class="content-divider-white">
    <div class="nav-tabs-custom">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs ui-sortable-handle">
            <li class="active"><a href="#user-settings" data-toggle="tab" aria-expanded="false">Edit Profile</a></li>
            <li><a href="#import-products" data-toggle="tab" aria-expanded="false">Import Products</a></li>
            <li><a href="#user-domains" data-toggle="tab" aria-expanded="false">Site Names</a></li>
            <li><a href="#user-password" data-toggle="tab" aria-expanded="false">Reset Password</a></li>
            <li><a href="#display-settings" data-toggle="tab" aria-expanded="true">Display Settings</a></li>
            @if(auth()->user()->needSubscription)
                <li><a href="#manage-subscription" data-toggle="tab" aria-expanded="true">Manage My Subscription</a>
                </li>
            @endif
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="user-settings">
                <div class="row">
                    <div class="col-lg-offset-3 col-lg-6 col-md-offset-1 col-md-10">
                        <div class="p-10">
                            @include('user.profile.forms.edit')
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="import-products">
                <div class="row">
                    <div class="col-lg-offset-1 col-lg-10 col-md-12">
                        <div class="p-10">
                            @include('products.import.index')
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="user-domains">
                <div class="row">
                    <div class="col-lg-offset-3 col-lg-6 col-md-offset-1 col-md-10">
                        <div class="p-10">
                            @include('user.domain.forms.edit')
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="user-password">
                <div class="row">
                    <div class="col-lg-offset-3 col-lg-6 col-md-offset-1 col-md-10">
                        <div class="p-10">

                            <h4 class="lead">Reset Password</h4>
                            <hr>
                            <p class="m-b-20">
                                By clicking the reset password button, an email with update password link will be sent
                                to <a href="mailto:{{$user->email}}">{{$user->email}}</a>. Click on the link to set a
                                new password and confirm. Your password will be automatically updated.
                            </p>
                            {!! Form::open(array('route' => 'password.post', 'method' => 'post', "id" => "frm-password", 'onsubmit' => 'submitForgotPassword(); return false;')) !!}

                            <ul class="text-danger errors-container">
                            </ul>

                            <input type="hidden" name="email" value="{{$user->email}}">

                            <div class="row m-b-20">
                                <div class="col-sm-12">
                                    <div class="g-recaptcha" data-sitekey="{{config('google_captcha.site_key')}}"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    {!! Form::submit('RESET PASSWORD', ["class"=>"btn btn-primary btn-flat", "href"=>"#"]) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="display-settings">
                <div class="row">
                    <div class="col-md-offset-3 col-md-6">
                        <div class="p-10">
                            <h4 class="lead">Date Time</h4>
                            <hr>
                            {!! Form::model(auth()->user()->preferences, array('route' => 'preference.mass_update', 'method' => 'put', 'id' => 'frm-display-settings', 'class' => 'sl-form-horizontal form-horizontal', 'onsubmit' => 'submitDisplaySettings(); return false;')) !!}
                            <div class="form-group">
                                <label for="" class="col-md-3 control-label">Date format</label>
                                <div class="col-md-9">
                                    <select name="preferences[DATE_FORMAT]" id="sel-date-format"
                                            class="form-control">
                                        <option value="j M y" {{auth()->user()->preference('DATE_FORMAT') == 'j M y' ? 'selected': ''}}>{{date('j M y')}}</option>
                                        <option value="Y-m-d" {{auth()->user()->preference('DATE_FORMAT') == 'Y-m-d' ? 'selected': ''}}>{{date('Y-m-d')}}</option>
                                        <option value="d F" {{auth()->user()->preference('DATE_FORMAT') == 'd F' ? 'selected': ''}}>{{date('d F')}}</option>
                                        <option value="j M Y" {{auth()->user()->preference('DATE_FORMAT') == 'j M Y' ? 'selected': ''}}>{{date('j M Y')}}</option>
                                        <option value="Ymd" {{auth()->user()->preference('DATE_FORMAT') == 'Ymd' ? 'selected': ''}}>{{date('Ymd')}}</option>
                                        <option value="Y-m-d" {{auth()->user()->preference('DATE_FORMAT') == 'Y-m-d' ? 'selected': ''}}>{{date('Y-m-d')}}</option>
                                        <option value="jS \of F Y" {{auth()->user()->preference('DATE_FORMAT') == 'jS \of F Y' ? 'selected': ''}}>{{date('jS \of F Y')}}</option>
                                        <option value="j F Y" {{auth()->user()->preference('DATE_FORMAT') == 'j F Y' ? 'selected': ''}}>{{date('j F Y')}}</option>
                                        <option value="F j, Y" {{auth()->user()->preference('DATE_FORMAT') == 'F j, Y' ? 'selected': ''}}>{{date('F j, Y')}}</option>
                                        <option value="d/m/Y" {{auth()->user()->preference('DATE_FORMAT') == 'd/m/Y' ? 'selected': ''}}>{{date('d/m/Y')}}</option>
                                        <option value="m/d/Y" {{auth()->user()->preference('DATE_FORMAT') == 'm/d/Y' ? 'selected': ''}}>{{date('m/d/Y')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-3 control-label">Time format</label>
                                <div class="col-md-9">
                                    <select name="preferences[TIME_FORMAT]" id="sel-time-format"
                                            class="form-control">
                                        <option value="g:i a" {{auth()->user()->preference('TIME_FORMAT') == 'g:i a' ? 'selected' : ''}}>{{date('g:i a')}}</option>
                                        <option value="h:i a" {{auth()->user()->preference('TIME_FORMAT') == 'h:i a' ? 'selected' : ''}}>{{date('h:i a')}}</option>
                                        <option value="g:i A" {{auth()->user()->preference('TIME_FORMAT') == 'g:i A' ? 'selected' : ''}}>{{date('g:i A')}}</option>
                                        <option value="h:i A" {{auth()->user()->preference('TIME_FORMAT') == 'h:i A' ? 'selected' : ''}}>{{date('h:i A')}}</option>
                                        <option value="H:i" {{auth()->user()->preference('TIME_FORMAT') == 'H:i' ? 'selected' : ''}}>{{date('H:i')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <div class="col-sm-12">
                                    {!! Form::submit('UPDATE', ["class"=>"btn btn-primary btn-flat", "href"=>"#"]) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="manage-subscription">
                <div class="row">
                    <div class="col-md-offset-2 col-md-8">
                        <div class="p-10 manage-subscription-container">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script type="text/javascript">
        $(function () {
            $('a[data-toggle="tab"][href="#manage-subscription"]').on('shown.bs.tab', function (e) {
                if ($(".manage-subscription-container").html().trim() == "") {
                    showLoading();
                    $.ajax({
                        "url": "{{route('subscription.index')}}",
                        "method": "get",
                        "success": function (html) {
                            hideLoading();
                            $(".manage-subscription-container").html(html);
                        },
                        "error": function (xhr, status, error) {
                            hideLoading();
                            describeServerRespondedError(xhr.status);
                        }
                    });
                }
            })
        })

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
                        gaResetPassword();
                        alertP('Email Sent', 'An email with reset password link has been sent to provided email address.', function () {
                            window.location.href = "{{route('login.get')}}";
                        });
                    } else {
                        grecaptcha.reset();
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    grecaptcha.reset();
                    if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                        var $errorContainer = $(".errors-container");
                        $errorContainer.empty();
                        $.each(xhr.responseJSON, function (key, error) {
                            $errorContainer.append(
                                    $("<li>").text(error)
                            );
                        });
                    } else {
                        describeServerRespondedError(xhr.status);
                    }
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
                        gaUpdateDateTime();
                        alertP("Display settings", "Your display settings have been successfully updated.");
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
                    describeServerRespondedError(xhr.status);
                }
            })
        }
    </script>
@stop