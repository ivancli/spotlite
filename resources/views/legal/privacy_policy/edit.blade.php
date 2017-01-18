@extends('layouts.adminlte')
@section('title', 'Manage Terms and Conditions')

@section('header_title', "Manage Terms and Conditions")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body">
                    {!! Form::model($privacyPolicy, array('route' => array('privacy_policy.update', $privacyPolicy->getKey()), 'method'=>'put', 'onsubmit' => 'submitUpdatePrivacyPolicy(this); return false;', 'id' => 'frm-edit-privacy-policy')) !!}
                    <input type="hidden" name="content" id="txt-privacy-policy-content">
                    <div class="row m-b-20">
                        <div class="col-sm-12">
                            <div id="privacy-policies-editor">
                                {!! $privacyPolicy->content !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button id="btn-save-privacy-policies" class="btn btn-primary btn-flat">SAVE</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript" src="{{asset('build/packages/ckeditor/ckeditor.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            CKEDITOR.replace('privacy-policies-editor');
        });

        function submitUpdatePrivacyPolicy(el) {
            var content = CKEDITOR.instances['privacy-policies-editor'].getData();
            if (content.length > 0) {
                $("#txt-privacy-policy-content").val(content);
                $.ajax({
                    "url": $(el).attr("action"),
                    "method": "put",
                    "data": $(el).serialize(),
                    "dataType": "json",
                    "success": function (response) {
                        if (response.status == true) {
                            alertP("Saved", "The privacy policy has been saved successfully.");
                        }
                    },
                    "error": function (xhr, status, error) {
                        describeServerRespondedError(xhr.status);
                    }
                });
            } else {
                alertP('Error', 'Please provide content for this privacy policy.');
                return false;
            }
        }
    </script>
@stop