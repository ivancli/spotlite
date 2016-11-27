<div class="form-group has-feedback">
    {!! Form::select('title', array(
    "Ms" => "Ms",
    "Mrs" => "Mrs",
    "Miss" => "Miss",
    "Mr" => "Mr",
    ), null, ['class'=>'form-control', 'placeholder' => "Title"]) !!}
</div>
<div class="form-group required">
    {!! Form::text('first_name', null, array('class' => 'form-control', 'placeholder' => 'First name')) !!}
</div>
<div class="form-group required">
    {!! Form::text('last_name', null, array('class' => 'form-control', 'placeholder' => 'Last name')) !!}
</div>
<div class="form-group has-feedback required">
    {!! Form::email('email', null, array('class' => 'form-control', 'placeholder' => 'Email')) !!}
    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
</div>
<div class="form-group has-feedback required">
    {!! Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password')) !!}
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>
<div class="form-group has-feedback required">
    {!! Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => 'Confirm password')) !!}
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>
{{--<div class="form-group required">--}}
    {{--{!! Form::select('industry', array(--}}
    {{--"Aerospace" => "Aerospace",--}}
    {{--"Agriculture" => "Agriculture",--}}
    {{--"Chemical" => "Chemical",--}}
    {{--"Computer" => "Computer",--}}
    {{--"Construction" => "Construction",--}}
    {{--"Defense" => "Defense",--}}
    {{--"Education" => "Education",--}}
    {{--"Energy" => "Energy",--}}
    {{--"Entertainment" => "Entertainment",--}}
    {{--"Financial services" => "Financial services",--}}
    {{--"Food" => "Food",--}}
    {{--"Health care" => "Health care",--}}
    {{--"Hospitality" => "Hospitality",--}}
    {{--"Information" => "Information",--}}
    {{--"Manufacturing" => "Manufacturing",--}}
    {{--"Mass media" => "Mass media",--}}
    {{--"Telecommunications" => "Telecommunications",--}}
    {{--"Transport" => "Transport",--}}
    {{--"Water" => "Water",--}}
    {{--), null, ['class'=>'form-control', 'placeholder' => "Industry"]) !!}--}}
{{--</div>--}}
{{--<div class="form-group required">--}}
    {{--{!! Form::select('company_type', array(--}}
    {{--"Retailer" => "Retailer",--}}
    {{--"Brand" => "Brand",--}}
    {{--"Other" => "Other"--}}
    {{--), null, ['class'=>'form-control', 'placeholder' => "Company type"]) !!}--}}
{{--</div>--}}
{{--<div class="form-group required">--}}
    {{--{!! Form::text('company_name', null, array('class' => 'form-control', 'placeholder' => 'Company name')) !!}--}}
{{--</div>--}}
<div class="form-group">
    {!! Form::text('coupon_code', null, array('class' => 'form-control', 'placeholder' => 'Coupon code')) !!}
</div>