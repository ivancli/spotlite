<div class="form-group required">
    {!! Form::label('email', 'Email', array('class' => 'control-label')) !!}
    {!! Form::email('email', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group required">
    {!! Form::label('password', 'Password', array('class' => 'control-label')) !!}
    {!! Form::password('password', array('class' => 'form-control')) !!}
</div>
