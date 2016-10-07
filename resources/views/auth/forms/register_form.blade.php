<div class="form-group">
    {!! Form::label('title', 'Title', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::select('title', array(
        "" => "Please select",
        "Ms" => "Ms",
        "Mrs" => "Mrs",
        "Miss" => "Miss",
        "Mr" => "Mr",
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
        {!! Form::email('email', null, array('class' => 'form-control')) !!}
    </div>
</div>
<div class="form-group required">
    {!! Form::label('password', 'Password', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::password('password', array('class' => 'form-control')) !!}
    </div>
</div>
<div class="form-group required">
    {!! Form::label('password_confirmation', 'Confirm password', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::password('password_confirmation', array('class' => 'form-control')) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('phone', 'Phone', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::text('phone', null, array('class' => 'form-control')) !!}
    </div>
</div>