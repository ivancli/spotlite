@extends('layouts.adminlte')
@section('title', 'Products')
@section('header_title', "Products")
@section('breadcrumbs')
    {!! Breadcrumbs::render('product_index') !!}
@stop
@section('content')
    @include('products.partials.banner_stats')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row m-b-10">
                        <div class="col-sm-12">
                            <a href="#" class="btn btn-default" onclick="toggleCollapseCategories();">Toggle Collapse
                                Categories</a>
                            <a href="#" class="btn btn-default" onclick="toggleCollapseProducts();">Toggle Collapse
                                Products</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row m-b-10">
                        <div class="col-sm-12">
                            <a href="#" class="btn btn-primary btn-xs" onclick="appendCreateCategoryBlock();"><i
                                        class="fa fa-plus"></i> Add Category</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 list-container">
                            {{--@foreach($categories as $category)--}}
                            {{--@include('products.category.partials.single_category')--}}
                            {{--@endforeach--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        var start = 0;
        var length = 5;
        var initLength = 10;
        var theEnd = false;

        $(function () {
            loadCategories(start, initLength, function (response) {
                $(".list-container").append(response.categoriesHTML);
            }, function (xhr, status, error) {

            });

            window.onscroll = function (ev) {
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                    if (!theEnd) {
                        loadCategories(start, initLength, function (response) {
                            $(".list-container").append(response.categoriesHTML);
                        }, function (xhr, status, error) {

                        });
                    }
                }
            };
        });

        function appendCreateCategoryBlock() {
            showLoading();
            var $list = $(".list-container")
            if ($list.find(".category-wrapper.create").length == 0) {
                $.get("{{route('category.create')}}", function (html) {
                    hideLoading();
                    $list.prepend(html);
                    $list.find(".category-wrapper.create .category-name").focus();
                });
            } else {
                hideLoading();
                $list.find(".category-wrapper.create .category-name").focus();
            }
        }

        function loadCategories(tStart, tLength, successCallback, failCallback) {
            showLoading();
            $.ajax({
                "url": "{{route("product.index")}}",
                "method": "get",
                "data": {
                    "start": tStart,
                    "length": tLength
                },
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        start += response.recordFiltered;
                        theEnd = response.recordFiltered < tLength;
                        if ($.isFunction(successCallback)) {
                            successCallback(response);
                        }
                    } else {
                        alertP("Error", "unable to load categories, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "unable to load categories, please try again later.");
                    if ($.isFunction(failCallback)) {
                        failCallback(xhr, status, error);
                    }
                }
            })
        }

        function resetFilters() {
            start = 0;
            length = 5;
        }

        function toggleCollapseCategories() {
            if ($(".collapsible-category-div").attr("aria-expanded") == "true") {
                $(".collapsible-category-div").attr("aria-expanded", false).removeClass("in")
            } else {
                $(".collapsible-category-div").attr("aria-expanded", true).addClass("in")
            }
        }

        function toggleCollapseProducts() {
            if ($(".collapsible-product-div").attr("aria-expanded") == "true") {
                $(".collapsible-product-div").attr("aria-expanded", false).removeClass("in")
            } else {
                $(".collapsible-product-div").attr("aria-expanded", true).addClass("in")
            }
        }
    </script>
@stop