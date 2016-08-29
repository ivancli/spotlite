<div class="form-group required">
    {!! Form::label('first_name', 'First name', array('class' => 'control-label')) !!}
    {!! Form::text('first_name', null, array('class' => 'form-control')) !!}
</div><div class="form-group required">
    {!! Form::label('last_name', 'Last name', array('class' => 'control-label')) !!}
    {!! Form::text('last_name', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group required">
    {!! Form::label('email', 'Email', array('class' => 'control-label')) !!}
    {!! Form::email('email', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group required">
    {!! Form::label('password', 'Password', array('class' => 'control-label')) !!}
    {!! Form::password('password', array('class' => 'form-control')) !!}
</div>
<div class="form-group required">
    {!! Form::label('password_confirmation', 'Confirm password', array('class' => 'control-label')) !!}
    {!! Form::password('password_confirmation', array('class' => 'form-control')) !!}
</div>