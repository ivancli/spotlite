@extends('layouts.adminlte')
@section('title', 'Register')

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Subscription Plans</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            @foreach($products as $item)
                                <div class="product-container"
                                     data-link="{{$item->product->public_signup_pages[0]->url}}">
                                    <div class="text-center">
                                        <img src="http://placehold.it/150x100" alt="">
                                    </div>
                                    {{--product_id: {{$item->product->id}}--}}
                                    <h4>{{$item->product->name}}</h4>
                                    <p>{{$item->product->description}}</p>
                                    {{--                                    expiration_interval: {{$item->product->expiration_interval}}--}}
                                    {{--expiration_interval_unit: {{$item->product->expiration_interval_unit}}--}}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-6">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Register</h3>
                </div>
                <div class="box-body">
                    <div class="um-form-container">
                        @if(isset($errors))
                            <ul class="text-danger">
                                @foreach ($errors->all('<li>:message</li>') as $message)
                                    {!! $message !!}
                                @endforeach
                            </ul>
                        @endif
                        {!! Form::open(array('route' => 'register.post', 'method' => 'post', "id" => "frm-register")) !!}
                        @include('auth.forms.register_form')
                        <input type="hidden" name="signup_link" id="txt-signup-link">
                        <div class="row m-b-5">
                            <div class="col-sm-6">
                                <a href="{{route('login.get')}}">Already have an account? Click here to login</a>
                            </div>
                            <div class="col-sm-6 text-right">
                                {!! Form::submit('Register', ["class"=>"btn btn-default btn-sm", "disabled" => "disabled", "id" => "btn-register"]) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(function () {
            $(".product-container").on("click", function () {
                $(".product-container.selected").removeClass("selected")
                $(this).addClass("selected");
                var link = $(this).attr("data-link");
                $("#txt-signup-link").val(link);
                updateBtnRegisterStatus();
            });
        });

        function updateBtnRegisterStatus() {
            $("#btn-register").prop("disabled", $(".product-container.selected").length == 0);
        }
    </script>
@stop