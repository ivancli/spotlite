<div class="form-group required">
    {!! Form::label('name', 'Group name', array('class' => 'control-label')) !!}
    {!! Form::text('name', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group required">
    {!! Form::label('url', 'URL', array('class' => 'control-label')) !!}
    {!! Form::text('url', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('description', 'Description', array('class' => 'control-label')) !!}
    {!! Form::text('description', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('active', 'Is active', array('class' => 'control-label')) !!}
    {!! Form::checkbox('active', '1', null) !!}
</div>