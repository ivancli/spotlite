@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::model($group, array('route' => array('group.update', $group->getKey()), 'method'=>'put')) !!}
@include('user.group.forms.group')
<div class="text-right">
    {!! Form::submit('Save', ["class"=>"btn btn-primary btn-sm"]) !!}
    <a href="{{route('group.index')}}" class="btn btn-default btn-sm">Cancel</a>
</div>
{!! Form::close() !!}