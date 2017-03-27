@extends('layouts.adminlte')
@section('title', 'Positioning')

@section('header_title', "Positioning")


@section('links')
    <link rel="stylesheet" href="{{elixir('css/tour.css')}}">
@stop

@section('head_scripts')
    {{--TOUR--}}
    @if(\Request::has('tour') && \Request::get('tour') == 'dashboard')
        <script type="text/javascript" src="{{elixir('js/dashboard-tour.js')}}"></script>
    @else
        <script type="text/javascript" src="{{elixir('js/product-tour.js')}}"></script>
    @endif
@stop

@section('breadcrumbs')
@stop

@section('content')
    <style>
        .sl-form-inline > div {
            margin-bottom: 15px;
        }

        select.form-control-inline {
            min-width: 150px;
        }
    </style>
    {{--@include('products.partials.banner_stats')--}}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body positioning-table-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <form action="" class="form-inline sl-form-inline" onsubmit="return false;">
                                <div class="col-sm-12">
                                    <label for="sel-reference" class="control-label">Reference site</label>&nbsp;
                                    <select name="" id="sel-reference" class="form-control form-control-inline">

                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="sel-position" class="control-label">Competitive position</label>&nbsp;
                                    <select name="" id="sel-position" class="form-control form-control-inline">
                                        <option value="">all products</option>
                                        <option value="">products where reference site is not cheapest</option>
                                        <option value="">products where reference site is most expensive</option>
                                        <option value="">products where reference site is cheapest</option>
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="sel-cateogries" class="control-label">Categories</label>&nbsp;
                                    <select name="" id="sel-cateogries" class="form-control form-control-inline">
                                        <option value="">all categories</option>
                                        {{--loop through all categories--}}
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="sel-exclude-competitors" class="control-label">Exclude competitors</label>&nbsp;
                                    <select name="" id="sel-exclude-competitors" multiple="multiple" class="form-control form-control-inline">
                                        {{--loop through all domains--}}
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="sel-brand" class="control-label">Brand</label>&nbsp;
                                    <select name="" id="sel-brand" class="form-control form-control-inline">
                                        {{--loop through all brands--}}
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="sel-suppliers" class="control-label">Supplier</label>&nbsp;
                                    <select name="" id="sel-suppliers" class="form-control form-control-inline">
                                        {{--loop through all suppliers--}}
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <button class="btn btn-primary">SHOW PRODUCTS</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(function () {
            $("select").select2();
        })
    </script>
@stop