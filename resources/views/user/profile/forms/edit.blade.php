@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::model($user, array('route' => array('profile.update', $user->getKey()), 'method'=>'put')) !!}
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
    {!! Form::email('email', null, array('class' => 'form-control', 'disabled' => 'disabled')) !!}
</div>
<div class="text-right">
    {!! Form::submit('Save', ["class"=>"btn btn-primary btn-sm"]) !!}
    <a href="{{route('um.user.index')}}" class="btn btn-default btn-sm">Cancel</a>
</div>
{!! Form::close() !!}