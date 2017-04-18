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
                The Positioning screen provides a powerful yet easy to use way of quickly seeing how your prices compare
                to the competitors across all categories and products.
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

        td.cel-product-name,
        td.cel-category-name {
            word-wrap: break-word;
            word-break: break-all;
        }

        tr.my-site td, tr.my-site td a {
            color: #43bda5 !important;
        }
    </style>
    {{--@include('products.partials.banner_stats')--}}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body positioning-table-content">
                    <div class="row m-b-20">
                        <div class="col-sm-12">
                            <form action="{{route('positioning.show')}}" id="frm-positioning" method="post"
                                  class="" onsubmit="showProducts(this); return false;">
                                <div class="col-md-4 form-group">
                                    <label for="sel-reference" class="control-label">Reference Site</label>&nbsp;
                                    <select name="reference" id="sel-reference"
                                            class="form-control">
                                        <option value=""></option>
                                        @foreach($domains as $domain=>$domainName)
                                            <option value="{{$domain}}"
                                                    @if(strpos(auth()->user()->company_url, $domain) !== false)
                                                    selected="selected"
                                                    @endif
                                            >{{$domainName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="sel-position" class="control-label">Competitive Position</label>&nbsp;
                                    <select name="position" id="sel-position" class="form-control">
                                        <option value="">All products</option>
                                        <option value="not_cheapest">Reference site not cheapest</option>
                                        <option value="most_expensive">Reference site most expensive</option>
                                        <option value="cheapest">Reference site is cheapest</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="sel-cateogries" class="control-label">Categories</label>&nbsp;
                                    <select name="category" id="sel-category" class="form-control">
                                        <option value="">All categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->getKey()}}">{{$category->category_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="sel-exclude-competitors" class="control-label">Exclude Competitors</label>&nbsp;
                                    <select name="exc_competitors[]" id="sel-exclude-competitors" multiple="multiple"
                                            class="form-control">
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="sel-brand" class="control-label">Brand</label>&nbsp;
                                    <select name="brand" id="sel-brand" class="form-control">
                                        <option value="">All brands</option>
                                        @foreach($brands as $brand)
                                            <option value="{{$brand}}">{{$brand}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="sel-suppliers" class="control-label">Supplier</label>&nbsp;
                                    <select name="supplier" id="sel-supplier" class="form-control">
                                        <option value="">All suppliers</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier}}">{{$supplier}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <button class="btn btn-primary btn-flat">SHOW PRODUCTS</button>
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
                                    <th width="90">Reference site price</th>
                                    <th>Cheapest</th>
                                    <th width="90">Cheapest $</th>
                                    <th width="90">Difference $</th>
                                    <th width="90">Difference %</th>
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
        var domains = {!! json_encode($domains) !!};

        $(function () {
            populateExcludeCompetitors();
            $("select").select2();
            $("#sel-reference").on("change", function () {
                populateExcludeCompetitors();
            });

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
                        d.exclude = $("#sel-exclude-competitors").val();

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
                        "class": 'cel-category-name',
                        "data": 'category_name'
                    },
                    {
                        "name": 'product_name',
                        "class": 'cel-product-name',
                        "data": 'product_name'
                    },
                    {
                        "name": 'reference.recent_price',
                        "data": function (data) {
                            if (typeof data.reference_recent_price != 'undefined' && data.reference_recent_price != null) {
                                return "$" + parseFloat(data.reference_recent_price).formatMoney(2, '.', ',');
                            } else {
                                return 'n/a'
                            }
                        }
                    },
                    {
                        "name": 'cheapest_site_url',
                        "data": function (data) {

                            if (typeof data.cheapest_site_url != 'undefined' && data.cheapest_site_url != null) {
                                console.info(data);
                                var site_urls_and_ebay = data.cheapest_site_url.split('$ $');
                                var $container = $("<div>");
                                $.each(site_urls_and_ebay, function (index, site_url_and_ebay) {
                                    var site_url = site_url_and_ebay.split('$#$')[0];
                                    if (site_url_and_ebay.split('$#$').length > 1 && site_url_and_ebay.split('$#$')[1] != '') {
                                        var ebay_username = site_url_and_ebay.split('$#$')[1];
                                    }
                                    console.info('ebay_username', ebay_username);
                                    $container.append(
                                        $("<div>").append(
                                            $("<a>").attr({
                                                "href": site_url,
                                                "target": "_blank"
                                            }).text(function () {
                                                var siteUrlText = site_url;
                                                if (typeof ebay_username != 'undefined') {
                                                    siteUrlText = ebay_username;
                                                } else {
                                                    $.each(domains, function (domain, domainName) {
                                                        if (site_url.indexOf(domain) > -1) {
                                                            siteUrlText = domainName
                                                        }
                                                    });
                                                }
                                                return siteUrlText;
                                            })
                                        )
                                    )
                                });
                                return $container.html();
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

                            if (typeof data.cheapest_recent_price != 'undefined' && typeof data.reference_recent_price != 'undefined' && data.cheapest_recent_price != null && data.reference_recent_price != null) {
                                return '$' + (Math.abs(parseFloat(data.reference_recent_price) - parseFloat(data.cheapest_recent_price))).formatMoney(2, '.', ',');
                            }
                            return 'n/a'
                        }
                    },
                    {
                        "name": 'percent_diff_ref_cheapest',
                        "data": function (data) {
                            if (typeof data.cheapest_recent_price != 'undefined' && typeof data.reference_recent_price != 'undefined' && data.cheapest_recent_price != null && data.reference_recent_price != null) {
                                return ((Math.abs(parseFloat(data.reference_recent_price) - parseFloat(data.cheapest_recent_price))) / parseFloat(data.reference_recent_price) * 100).formatMoney(2, '.', ',') + '%';
                            }
                            return 'n/a'
                        }
                    }
                ],
                "initComplete": function (settings, json) {
                    $(".dataTables_empty").text('Click "SHOW PRODUCTS" button to load products.');
                },
                "rowCallback": function (row, data, index) {

                    if (typeof data.cheapest_site_url != 'undefined' && data.cheapest_site_url != null) {
                        console.info('data', data);
                        var site_urls = data.cheapest_site_url.split('$ $');
                        var reference = $("#sel-reference").val();
                        var isMySite = false;
                        if (reference) {
                            $.each(site_urls, function (index, site_url) {
                                site_url = site_url.split('$#$')[0];
                                if (site_url.indexOf(reference) > -1) {
                                    isMySite = true;
                                }
                            });
                        }
                        if (isMySite) {
                            $(row).addClass("my-site");
                        }
                    }
                }
            });
        });

        function populateExcludeCompetitors() {
            var $selExclude = $("#sel-exclude-competitors");
            var excludeValue = $selExclude.val();
            $selExclude.empty();
            var reference = $("#sel-reference").val();
            $.each(domains, function (domain, domainName) {
                if (domain != reference) {
                    $selExclude.append(
                        $("<option>").attr({
                            "value": domain
                        }).text(domainName)
                    )
                }
            });
            $selExclude.select2();
            $selExclude.val(excludeValue).trigger('change');
        }

        function showProducts(el) {
            tblProducts.ajax.reload();
        }
    </script>
@stop