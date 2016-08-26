<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/15/2016
 * Time: 10:54 AM
 */
?>

<div>
    {!! Form::label('first_name', 'First name') !!}
    {!! Form::text('first_name', old('name'), ['class' => 'form-control input-sm', 'placeholder' => 'full name']) !!}
</div>
<div>
    {!! Form::label('email', 'Email') !!}
    {!! Form::text('email', old('email'), ['class' => 'form-control input-sm', 'placeholder' => 'email', 'disabled' => 'disabled']) !!}
</div>

<div>
    {!! Form::label('status', 'Status') !!} &nbsp;
    {!! Form::select('status', array('active' => 'active', 'inactive' => 'inactive', 'locked' => 'locked', 'deleted' => 'deleted'), old('status'), ['class' => 'control-inline form-control input-sm m-b-5']) !!}
</div>