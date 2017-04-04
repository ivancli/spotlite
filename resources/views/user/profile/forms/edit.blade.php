<ul class="text-danger errors-container">
</ul>

{!! Form::model($user, array('route' => array('profile.update', $user->getKey()), 'method'=>'put', "id"=>"frm-profile-update", "onsubmit"=>"return false;", "class" => "form-horizontal sl-form-horizontal")) !!}
<div class="form-group">
    <h4 class="col-sm-12 font-weight-bold">My Details</h4>
</div>
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
<div class="form-group">
    <h4 class="col-sm-12 font-weight-bold">Company's Details</h4>
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
    <h4 class="col-sm-12 font-weight-bold">Display Preferences</h4>
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
<div class="form-group">
    <h4 class="col-sm-12 font-weight-bold">SpotLite Digest</h4>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="digest" id="chk-digest" onclick="updateDigestVisibility();" {{!is_null($user->reportTask) ? 'checked="checked"' : ''}}>
                I'd like to receive the SpotLite Digest on my email.
            </label>
        </div>
    </div>
</div>
<div class="digest-container" style="display: none;">
    <div class="form-group">
        <label for="" class="col-md-3 control-label">Frequency</label>
        <div class="col-md-9">
            <select name="frequency" id="sel-digest-type" class="form-control" onchange="updateDigestVisibility();">
                <option value="daily" {{!is_null($user->reportTask) && $user->reportTask->frequency == 'daily' ? 'selected="selected"' : ''}}>Daily</option>
                <option value="weekly" {{!is_null($user->reportTask) && $user->reportTask->frequency == 'weekly' ? 'selected="selected"' : ''}}>Weekly</option>
            </select>
        </div>
    </div>
    <div class="daily-conf-container" style="display: none;">
        <div class="form-group">
            <label for="" class="col-md-3 control-label">Time</label>
            <div class="col-md-9">
                <select name="time" class="form-control">
                    <option value="00:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '00:00:00' ? 'selected="selected"' : ''}}>12:00 am</option>
                    <option value="1:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '1:00:00' ? 'selected="selected"' : ''}}>1:00 am</option>
                    <option value="2:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '2:00:00' ? 'selected="selected"' : ''}}>2:00 am</option>
                    <option value="3:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '3:00:00' ? 'selected="selected"' : ''}}>3:00 am</option>
                    <option value="4:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '4:00:00' ? 'selected="selected"' : ''}}>4:00 am</option>
                    <option value="5:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '5:00:00' ? 'selected="selected"' : ''}}>5:00 am</option>
                    <option value="6:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '6:00:00' ? 'selected="selected"' : ''}}>6:00 am</option>
                    <option value="7:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '7:00:00' ? 'selected="selected"' : ''}}>7:00 am</option>
                    <option value="8:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '8:00:00' ? 'selected="selected"' : ''}}>8:00 am</option>
                    <option value="9:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '9:00:00' ? 'selected="selected"' : ''}}>9:00 am</option>
                    <option value="10:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '10:00:00' ? 'selected="selected"' : ''}}>10:00 am</option>
                    <option value="11:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '11:00:00' ? 'selected="selected"' : ''}}>11:00 am</option>
                    <option value="12:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '12:00:00' ? 'selected="selected"' : ''}}>12:00 pm</option>
                    <option value="13:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '13:00:00' ? 'selected="selected"' : ''}}>1:00 pm</option>
                    <option value="14:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '14:00:00' ? 'selected="selected"' : ''}}>2:00 pm</option>
                    <option value="15:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '15:00:00' ? 'selected="selected"' : ''}}>3:00 pm</option>
                    <option value="16:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '16:00:00' ? 'selected="selected"' : ''}}>4:00 pm</option>
                    <option value="17:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '17:00:00' ? 'selected="selected"' : ''}}>5:00 pm</option>
                    <option value="18:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '18:00:00' ? 'selected="selected"' : ''}}>6:00 pm</option>
                    <option value="19:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '19:00:00' ? 'selected="selected"' : ''}}>7:00 pm</option>
                    <option value="20:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '20:00:00' ? 'selected="selected"' : ''}}>8:00 pm</option>
                    <option value="21:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '21:00:00' ? 'selected="selected"' : ''}}>9:00 pm</option>
                    <option value="22:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '22:00:00' ? 'selected="selected"' : ''}}>10:00 pm</option>
                    <option value="23:00:00" {{!is_null($user->reportTask) && $user->reportTask->time == '23:00:00' ? 'selected="selected"' : ''}}>11:00 pm</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="weekday" {{ !is_null($user->reporTask) && $user->reportTask->weekday == 'y' ? 'checked="checked"' : '' }}> Weekday only
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="weekly-conf-container" style="display: none;">
        <div class="form-group">
            <label for="" class="col-md-3 control-label">Day</label>
            <div class="col-md-9">
                <select name="day" class="form-control">
                    <option value="1" {{ !is_null($user->reportTask) && $user->reportTask->day == '1' ? 'selected="selected"' : '' }}>Monday</option>
                    <option value="2" {{ !is_null($user->reportTask) && $user->reportTask->day == '2' ? 'selected="selected"' : '' }}>Tuesday</option>
                    <option value="3" {{ !is_null($user->reportTask) && $user->reportTask->day == '3' ? 'selected="selected"' : '' }}>Wednesday</option>
                    <option value="4" {{ !is_null($user->reportTask) && $user->reportTask->day == '4' ? 'selected="selected"' : '' }}>Thursday</option>
                    <option value="5" {{ !is_null($user->reportTask) && $user->reportTask->day == '5' ? 'selected="selected"' : '' }}>Friday</option>
                    <option value="6" {{ !is_null($user->reportTask) && $user->reportTask->day == '6' ? 'selected="selected"' : '' }}>Saturday</option>
                    <option value="7" {{ !is_null($user->reportTask) && $user->reportTask->day == '7' ? 'selected="selected"' : '' }}>Sunday</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="text-right">
    {!! Form::submit('UPDATE', ["class"=>"btn btn-primary btn-flat", "href"=>"#", "onclick"=>"profileUpdateOnClick();"]) !!}
</div>
{!! Form::close() !!}

<script type="text/javascript">
    $(function () {
        updateDigestVisibility();
    });

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
                    $.each(error, function (index, message) {
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

    function updateDigestVisibility() {
        var $chkDigest = $("#chk-digest");
        if ($chkDigest.is(":checked")) {
            $(".digest-container").slideDown();
        } else {
            $(".digest-container").slideUp();
        }
        var $digestType = $("#sel-digest-type");
        if ($digestType.val() == 'daily') {
            $(".daily-conf-container").slideDown();
            $(".weekly-conf-container").slideUp();
        } else {
            $(".daily-conf-container").slideUp();
            $(".weekly-conf-container").slideDown();
        }
    }
</script>