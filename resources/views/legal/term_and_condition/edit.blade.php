@extends('layouts.adminlte')
@section('title', 'Manage Terms and Conditions')

@section('header_title', "Manage Terms and Conditions")

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body">
                    {!! Form::model($termAndCondition, array('route' => array('term_and_condition.update', $termAndCondition->getKey()), 'method'=>'put', 'onsubmit' => 'submitUpdateTermAndCondition(this); return false;', 'id' => 'frm-edit-term-and-condition')) !!}
                    <input type="hidden" name="content" id="txt-term-and-condition-content">
                    <div class="row m-b-20">
                        <div class="col-sm-12">
                            <div id="terms-and-conditions-editor">
                                {!! $termAndCondition->content !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button id="btn-save-terms-and-conditions" class="btn btn-primary">SAVE</button>
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
            CKEDITOR.replace('terms-and-conditions-editor');
        });

        function submitUpdateTermAndCondition(el) {
            var content = CKEDITOR.instances['terms-and-conditions-editor'].getData();
            if (content.length > 0) {
                $("#txt-term-and-condition-content").val(content);
                $.ajax({
                    "url": $(el).attr("action"),
                    "method": "put",
                    "data": $(el).serialize(),
                    "dataType": "json",
                    "success": function (response) {
                        if (response.status == true) {
                            alertP("Saved", "The term and condition has been saved successfully.");
                        }
                    },
                    "error": function (xhr, status, error) {
                        describeServerRespondedError(xhr.status);
                    }
                });
            } else {
                alertP('Error', 'Please provide content for this term and condition.');
                return false;
            }
        }
    </script>
@stop