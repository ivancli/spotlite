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
