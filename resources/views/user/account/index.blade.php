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
            <li><a href="#import-products" data-toggle="tab" aria-expanded="false">Bulk Import</a></li>
            <li><a href="#user-domains" data-toggle="tab" aria-expanded="false">Site Names</a></li>
            <li><a href="#user-password" data-toggle="tab" aria-expanded="false">Reset Password</a></li>
            {{--<li><a href="#display-settings" data-toggle="tab" aria-expanded="true">Display Settings</a></li>--}}
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

            $(window).on("hashchange", function () {
                if (window.location.hash) {
                    $('a[data-toggle="tab"]').filter(function () {
                        return $(this).attr("href") == window.location.hash;
                    }).tab("show");
                }
            });
            if (window.location.hash) {
                $('a[data-toggle="tab"]').filter(function () {
                    return $(this).attr("href") == window.location.hash;
                }).tab("show");
            }
        });

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