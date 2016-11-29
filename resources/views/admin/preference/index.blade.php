@extends('layouts.adminlte')
@section('title', 'App Preferences')
@section('header_title', 'App Preferences')
@section('breadcrumbs')
    {{--{!! Breadcrumbs::render('admin_preference') !!}--}}
@stop
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    {!! Form::open(array('route' => 'admin.app_preference.update', 'method' => 'put', "id" => "frm-password", 'onsubmit' => 'submitUpdateAdminPreferences(this); return false;', 'class'=>'form-horizontal')) !!}
                    @foreach($appPreferences as $appPreference)
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">{{$appPreference->element}}</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="prefs[{{$appPreference->element}}]"
                                       value="{{$appPreference->value}}">
                            </div>
                        </div>
                    @endforeach
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            {!! Form::submit('Update Preferences', ["class"=>"btn btn-primary btn-flat", "href"=>"#"]) !!}
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
        function submitUpdateAdminPreferences(el) {
            showLoading();
            $.ajax({
                "url": $(el).attr("action"),
                "method": "put",
                "data": $(el).serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        alertP("Update Preferences", "App preferences are updated.");
                    } else {
                        alertP("Error", "Unable to update app preferences, please try again later.");
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