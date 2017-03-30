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
        "Accommodations" => "Accommodations",
        "Accounting" => "Accounting",
        "Advertising" => "Advertising",
        "Aerospace" => "Aerospace",
        "Agriculture & Agribusiness" => "Agriculture & Agribusiness",
        "Air Transportation" => "Air Transportation",
        "Apparel & Accessories" => "Apparel & Accessories",
        "Auto" => "Auto",
        "Banking" => "Banking",
        "Beauty & Cosmetics" => "Beauty & Cosmetics",
        "Biotechnology" => "Biotechnology",
        "Chemical" => "Chemical",
        "Communications" => "Communications",
        "Computer" => "Computer",
        "Construction" => "Construction",
        "Consulting" => "Consulting",
        "Consumer Electronics" => "Consumer Electrnics",
        "Education" => "Education",
        "Employment" => "Employment",
        "Energy" => "Energy",
        "Entertainment & Recreation" => "Entertainment & Recreation",
        "Fashion" => "Fashion",
        "Financial Services" => "Financial Services",
        "Fine Arts" => "Fine Arts",
        "Food & Beverage" => "Food & Beverage",
        "Health" => "Health",
        "Information" => "Information",
        "Information Technology" => "Information Technology",
        "Insurance" => "Insurance",
        "Journalism & News" => "Journalism & News",
        "Legal Services" => "Legal Services",
        "Manufacturing" => "Manufacturing",
        "Media & Broadcasting" => "Media & Broadcasting",
        "Medical Devices & Supplies" => "Medical Devices & Supplies",
        "Motion Pictures & Video" => "Motion Pictures & Video",
        "Music" => "Music",
        "Pharmaceutical" => "Pharmaceutical",
        "Public Administration" => "Public Administration",
        "Public Relations" => "Public Relations",
        "Publishing" => "Publishing",
        "Real Estate" => "Real Estate",
        "Retail" => "Retail",
        "Service" => "Service",
        "Sports" => "Sports",
        "Technology" => "Technology",
        "Telecommunications" => "Telecommunications",
        "Tourism" => "Tourism",
        "Transportation" => "Transportation",
        "Travel" => "Travel",
        "Utilities" => "Utilities",
        "Video Game" => "Video Game",
        "Web Services" => "Web Services",
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
<div class="form-group">
    {!! Form::label('company_url', 'My Site URL', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::text('company_url', null, array('class' => 'form-control', 'placeholder' => 'e.g. http://www.example.com')) !!}
    </div>
</div>

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

<div class="text-right">
    {!! Form::submit('UPDATE', ["class"=>"btn btn-primary btn-flat", "href"=>"#", "onclick"=>"profileUpdateOnClick();"]) !!}
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
            }
        }, function (xhr, status, error) {
            hideLoading();
            if (xhr.status == 422) {
                var $errorContainer = $("#user-settings").find(".errors-container");
                clearErrorMessgae();
                $.each(xhr.responseJSON, function (index, error) {
                    $.each(error, function(index, message){
                        $errorContainer.append(
                                $("<li>").text(message)
                        );
                    })
                });
            } else {
                describeServerRespondedError(xhr.status);
            }
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