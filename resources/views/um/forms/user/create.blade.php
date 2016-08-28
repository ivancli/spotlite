@if(isset($errors))
    <ul class="text-danger">
        @foreach ($errors->all('<li>:message</li>') as $message)
            {!! $message !!}
        @endforeach
    </ul>
@endif

{!! Form::open(array('route' => 'um.user.store', 'method'=>'post')) !!}
@include('um.forms.user.user')
<div class="text-right">
    {!! Form::submit('Create', ["class"=>"btn btn-primary btn-sm"]) !!}
    <a href="{{route('um.user.index')}}" class="btn btn-default btn-sm">Cancel</a>
</div>
{!! Form::close() !!}
