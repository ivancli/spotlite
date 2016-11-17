<ul class="text-danger errors-container">
</ul>

{!! Form::model($user, array('route' => array('profile.update', $user->getKey()), 'method'=>'put', "id"=>"frm-profile-update", "onsubmit"=>"return false;", "class" => "form-horizontal sl-form-horizontal")) !!}
<div class="form-group">
    {!! Form::label('title', 'Title', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::select('title', array(
        "" => "Please select",
        "Miss" => "Miss",
        "Mr" => "Mr",
        "Mrs" => "Mrs",
        "Ms" => "Ms",
        ), null, ['class'=>'form-control sl-form-control']) !!}
    </div>
</div>
<div class="form-group required">
    {!! Form::label('first_name', 'First name', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::text('first_name', null, array('class' => 'form-control')) !!}
    </div>
</div>
<div class="form-group required">
    {!! Form::label('last_name', 'Last name', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::text('last_name', null, array('class' => 'form-control')) !!}
    </div>
</div>
<div class="form-group required">
    {!! Form::label('email', 'Email', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::email('email', null, array('class' => 'form-control', 'disabled' => 'disabled')) !!}
    </div>
</div>
<div class="form-group required">
    {!! Form::label('industry', 'Industry', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::select('industry', array(
        "Aerospace" => "Aerospace",
        "Agriculture" => "Agriculture",
        "Chemical" => "Chemical",
        "Computer" => "Computer",
        "Construction" => "Construction",
        "Defense" => "Defense",
        "Education" => "Education",
        "Energy" => "Energy",
        "Entertainment" => "Entertainment",
        "Financial services" => "Financial services",
        "Food" => "Food",
        "Health care" => "Health care",
        "Hospitality" => "Hospitality",
        "Information" => "Information",
        "Manufacturing" => "Manufacturing",
        "Mass media" => "Mass media",
        "Telecommunications" => "Telecommunications",
        "Transport" => "Transport",
        "Water" => "Water",
        ), null, ['class'=>'form-control', 'placeholder' => "Industry"]) !!}
    </div>
</div>
<div class="form-group required">
    {!! Form::label('company_type', 'Company type', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::select('company_type', array(
        "Retailer" => "Retailer",
        "Brand" => "Brand",
        "Other" => "Other"
        ), null, ['class'=>'form-control', 'placeholder' => "Company type"]) !!}
    </div>
</div>
<div class="form-group required">
    {!! Form::label('company_name', 'Company name', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::text('company_name', null, array('class' => 'form-control', 'placeholder' => 'Company name')) !!}
    </div>
</div>


<div class="text-right">
    {!! Form::submit('Save', ["class"=>"btn btn-primary btn-sm btn-flat", "href"=>"#", "onclick"=>"profileUpdateOnClick();"]) !!}
</div>
{!! Form::close() !!}

<script type="text/javascript">
    function profileUpdateOnClick() {
        clearErrorMessgae();
        showLoading();
        submitProfileUpdate(function (response) {
            hideLoading();
            if (response.status == true) {
                gaUpdateUserProfile();
                alertP("Update Profile", "Profile has been updated.");
            } else {
                if (typeof response.errors != 'undefined') {
                    var $errorContainer = $(".errors-container");
                    clearErrorMessgae();
                    $.each(response.errors, function (index, error) {
                        $errorContainer.append(
                                $("<li>").text(error)
                        );
                    });
                } else {
                    alertP("Error", "Unable to update profile, please try again later.");
                }
            }
        }, function () {
            hideLoading();
            alertP("Error", "Unable to update profile, please try again later.");
        })
    }

    function submitProfileUpdate(successCallback, errorCallback) {
        $.ajax({
            "url": $("#frm-profile-update").attr("action"),
            "method": "put",
            "data": $("#frm-profile-update").serialize(),
            "dataType": "json",
            "success": successCallback,
            "error": errorCallback
        })
    }

    function clearErrorMessgae() {
        var $errorContainer = $(".errors-container");
        $errorContainer.empty();
    }
</script>