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
                                     data-link="{{$item->product->public_signup_pages[0]->url}}"
                                     data-id="{{$item->product->id}}">
                                    <div class="text-center">
                                        <img src="http://placehold.it/150x100" alt="">
                                    </div>
                                    {{--product_id: {{$item->product->id}}--}}
                                    <h4>{{$item->product->name}}</h4>
                                    <p>{{$item->product->description}}</p>
                                    <h4 class="text-center">Price:
                                        ${{number_format($item->product->price_in_cents/100, 2)}}
                                    </h4>
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
                        <input type="hidden" name="api_product_id" id="txt-api-product-id">
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
                $(".product-container.selected").removeClass("selected");
                $(this).addClass("selected");
                var link = $(this).attr("data-link");
                var apiProductID = $(this).attr("data-id");
                $("#txt-signup-link").val(link);
                $("#txt-api-product-id").val(apiProductID);
                updateBtnRegisterStatus();
            });
        });

        function updateBtnRegisterStatus() {
            $("#btn-register").prop("disabled", $(".product-container.selected").length == 0);
        }
    </script>
@stop