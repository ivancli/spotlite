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
    <div class="row">
        <div class="col-sm-12">
            <p class="text-muted font-size-17">
                The Positioning screen provides a powerful yet easy to use way of quickly seeing how your prices compare to the competitors across all categories and products.
            </p>
        </div>
    </div>
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
                    <div class="row m-b-20">
                        <div class="col-sm-12">
                            <form action="{{route('positioning.show')}}" id="frm-positioning" method="post" class="form-inline sl-form-inline" onsubmit="showProducts(this); return false;">
                                <div class="col-sm-12">
                                    <label for="sel-reference" class="control-label">Reference site</label>&nbsp;
                                    <select name="reference" id="sel-reference" class="form-control form-control-inline">
                                        <option value=""></option>
                                        @foreach($domains as $domain)
                                            <option value="{{$domain}}"
                                                    @if(strpos(auth()->user()->company_url, $domain) !== false)
                                                    selected="selected"
                                                    @endif
                                            >{{$domain}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="sel-position" class="control-label">Competitive position</label>&nbsp;
                                    <select name="position" id="sel-position" class="form-control form-control-inline">
                                        <option value="">all products</option>
                                        <option value="not_cheapest">products where reference site is not cheapest</option>
                                        <option value="most_expensive">products where reference site is most expensive</option>
                                        <option value="cheapest">products where reference site is cheapest</option>
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="sel-cateogries" class="control-label">Categories</label>&nbsp;
                                    <select name="category" id="sel-category" class="form-control form-control-inline">
                                        <option value="">all categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->getKey()}}">{{$category->category_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="sel-exclude-competitors" class="control-label">Exclude competitors</label>&nbsp;
                                    <select name="exc_competitors[]" id="sel-exclude-competitors" multiple="multiple" class="form-control form-control-inline">
                                        @foreach($domains as $domain)
                                            <option value="{{$domain}}">{{$domain}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="sel-brand" class="control-label">Brand</label>&nbsp;
                                    <select name="brand" id="sel-brand" class="form-control form-control-inline">
                                        <option value="">all brands</option>
                                        @foreach($brands as $brand)
                                            <option value="{{$brand}}">{{$brand}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="sel-suppliers" class="control-label">Supplier</label>&nbsp;
                                    <select name="supplier" id="sel-supplier" class="form-control form-control-inline">
                                        <option value="">all suppliers</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier}}">{{$supplier}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <button class="btn btn-primary">SHOW PRODUCTS</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <table id="tbl-products" class="table table-bordered table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Product</th>
                                    <th>Reference site price</th>
                                    <th>Cheapest site</th>
                                    <th>Cheapest site price</th>
                                    <th>Diff Cheapest</th>
                                    <th>%Diff Cheapest</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        var tblProducts = null;

        $(function () {
            $("select").select2();

            tblProducts = $("#tbl-products").DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "stateSave": true,
                "pageLength": 25,
                "order": [[0, "asc"]],
                "deferLoading": 0,
                "language": {
                    "emptyTable": "No domains in the list",
                    "zeroRecords": "No domains in the list"
                },
                "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'<\"toolbar-bottom-left\">><'col-sm-7'p>>",
                "ajax": {
                    "url": "{{route('positioning.show')}}",
                    "type": "POST",
                    "data": function (d) {
                        d.reference = $("#sel-reference").val();
                        d.position = $("#sel-position").val();
                        d.category = $("#sel-category").val();
                        d.brand = $("#sel-brand").val();
                        d.supplier = $("#sel-supplier").val();

                        $.each(d.order, function (index, order) {
                            if (typeof d.columns[d.order[index].column] != "undefined") {
                                d.order[index].column = d.columns[d.order[index].column].name;
                            }
                        });
                    },
                    "error": function (xhr, status, error) {
                        if (xhr.responseJSON != null && typeof xhr.responseJSON != 'undefined') {
                            var errorMsg = "";
                            $.each(xhr.responseJSON, function (key, error) {
                                $.each(error, function (index, message) {
                                    errorMsg += message + " ";
                                })
                            });
                            alertP("Oops! Something went wrong.", errorMsg);
                        } else {
                            describeServerRespondedError(xhr.status);
                        }
                    }
                },
                "columns": [
                    {
                        "name": 'category_name',
                        "data": 'category_name'
                    },
                    {
                        "name": 'product_name',
                        "data": 'product_name'
                    },
                    {
                        "name": 'reference.recent_price',
                        "data": function (data) {
//                            if (typeof data.recent_price != 'undefined' && data.recent_price != null) {
//                                return (data.recent_price).formatMoney(2, '.', ',');
//                            } else {
//                                return 'n/a'
//                            }
                            return "$" + (Math.random()*100).formatMoney(2, '.', ',');
                        }
                    },
                    {
                        "name": 'cheapest_site_url',
                        "data": function (data) {
                            if (typeof data.cheapest_site_url != 'undefined') {
                                return data.cheapest_site_url;
                            }
                            return 'n/a'
                        }
                    },
                    {
                        "name": 'cheapest_recent_price',
                        "data": function (data) {
                            if (typeof data.cheapest_recent_price != 'undefined') {
                                if (data.cheapest_recent_price != null) {
                                    return "$" + parseFloat((data.cheapest_recent_price)).formatMoney(2, '.', ',');
                                }
                            }
                            return 'n/a'
                        }
                    },
                    {
                        "name": 'diff_ref_cheapest',
                        "data": function (data) {

                            if (typeof data.cheapestSites != 'undefined' && data.cheapestSites.length > 0) {
                                if (data.cheapestSites[0].recent_price != null) {
                                    if (typeof data.recent_price != 'undefined' && data.recent_price != null) {
                                        return Math.abs(parseFloat(data.recent_price) - parseFloat(data.cheapestSites[0].recent_price));
                                    }
                                }
                            }
                            return 'n/a'
                        }
                    },
                    {
                        "name": 'percent_diff_ref_cheapest',
                        "data": function (data) {

                            if (typeof data.cheapestSites != 'undefined' && data.cheapestSites.length > 0) {
                                if (data.cheapestSites[0].recent_price != null) {
                                    if (typeof data.recent_price != 'undefined' && data.recent_price != null) {
                                        return (Math.abs(parseFloat(data.recent_price) - parseFloat(data.cheapestSites[0].recent_price))) / parseFloat(data.recent_price);
                                    }
                                }
                            }
                            return 'n/a'
                        }
                    }
                ],
                "initComplete": function (settings, json) {
                    $(".dataTables_empty").text('Click "SHOW PRODUCTS" button to load products.');
                }
            });
        });

        function showProducts(el) {
            tblProducts.ajax.reload();
        }
    </script>
@stop