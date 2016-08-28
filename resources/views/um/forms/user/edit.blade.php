@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::model($user, array('route' => array('um.user.update', $user->getKey()), 'method'=>'put')) !!}
@include('um.forms.user.user')
<div class="text-right">
    {!! Form::submit('Save', ["class"=>"btn btn-primary btn-sm"]) !!}
    <a href="{{route('um.user.index')}}" class="btn btn-default btn-sm">Cancel</a>
</div>
{!! Form::close() !!}