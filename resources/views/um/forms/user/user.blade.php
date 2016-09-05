<div class="form-group required">
    {!! Form::label('first_name', 'First name', array('class' => 'control-label')) !!}
    {!! Form::text('first_name', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group required">
    {!! Form::label('last_name', 'Last name', array('class' => 'control-label')) !!}
    {!! Form::text('last_name', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group required">
    {!! Form::label('email', 'Email', array('class' => 'control-label')) !!}
    {!! Form::email('email', null, array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('password', 'Password', array('class' => 'control-label')) !!}
    {!! Form::password('password', array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('password_confirmation', 'Confirm password', array('class' => 'control-label')) !!}
    {!! Form::password('password_confirmation', array('class' => 'form-control')) !!}
</div>
<div class="form-group">
    {!! Form::label('group_id[]', 'Groups', array('class' => 'control-label')) !!}
    {!! Form::select('group_id[]', $groups, isset($user) && !is_null($user->groups) ? $user->groups->pluck((new \App\Models\Group())->getKeyName())->toArray() : null, ['class'=>'form-control', 'multiple' => 'multiple', 'size'=>10]) !!}
</div>

<div class="form-group">
    {!! Form::label('role_id[]', 'Roles', array('class' => 'control-label')) !!}
    {!! Form::select('role_id[]', $roles, isset($user) && !is_null($user->roles) ? $user->roles->pluck((new \Invigor\UM\UMRole())->getKeyName())->toArray() : null, ['class'=>'form-control', 'multiple' => 'multiple', 'size'=>10]) !!}
</div>