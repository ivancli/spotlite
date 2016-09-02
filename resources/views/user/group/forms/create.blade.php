@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::open(array('route' => array('group.store'), 'method'=>'post')) !!}
@include('user.group.forms.group')
<div class="text-right">
    {!! Form::submit('Create', ["class"=>"btn btn-primary btn-sm"]) !!}
    <a href="{{route('group.index')}}" class="btn btn-default btn-sm">Cancel</a>
</div>
{!! Form::close() !!}