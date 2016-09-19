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
                <div class="box-header with-border">
                    <h3 class="box-title">Product List</h3>
                    <div class="box-tools pull-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="true">
                                <i class="fa fa-bars"></i>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>
                                    <a href="#" class="btn btn-default" onclick="toggleCollapseCategories();">
                                        Toggle Collapse Categories
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-default" onclick="toggleCollapseProducts();">
                                        Toggle Collapse Products
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row m-b-10">
                        <div class="col-sm-12">
                            <a href="#" class="btn btn-primary btn-xs" onclick="appendCreateCategoryBlock();"><i
                                        class="fa fa-plus"></i> Add Category</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 list-container">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')

    <script type="text/javascript" src="{{elixir('js/highcharts.js')}}"></script>
    <script type="text/javascript">
        var start = 0;
        var length = 5;
        var initLength = 10;
        var theEnd = false;
        /**
         * drag and drop source
         */
        var drag_source = null;
        var draggedType = null;
        var categoryDrake = null;

        $(function () {

            /**
             * category drag and drop
             */
            categoryDrake = dragula([$(".list-container").get(0)], {
                moves: function (el, container, handle) {
                    return $(handle).hasClass("btn-category-dragger") || $(handle).closest(".btn-category-dragger").length > 0;
                }
            }).on('drop', function (el, target, source, sibling) {
                updateCategoryOrder();
            });

            /** enable scrolling when dragging */
            autoScroller([window], {
                margin: 20,
                pixels: 20,
                scrollWhenOutside: true,
                autoScroll: function () {
                    //Only scroll when the pointer is down, and there is a child being dragged.
                    return this.down && categoryDrake.dragging;
                }
            });


            loadCategories(start, initLength, function (response) {
                $(".list-container").append(response.categoriesHTML);
            }, function (xhr, status, error) {

            });
            $(window).scroll(function () {
                if (Math.round($(window).scrollTop() + $(window).height()) == $(document).height()) {
                    if (!theEnd) {
                        loadCategories(start, initLength, function (response) {
                            $(".list-container").append(response.categoriesHTML);
                        }, function (xhr, status, error) {

                        });
                    }
                }
            });
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

        function assignCategoryOrderNumber() {
            $(".category-wrapper").each(function (index) {
                $(this).attr("data-order", index + 1);
            });
        }

        function updateCategoryOrder() {
            assignCategoryOrderNumber();
            var orderList = [];
            $(".category-wrapper").filter(function () {
                return !$(this).hasClass("gu-mirror");
            }).each(function () {
                if ($(this).attr("data-category-id")) {
                    var categoryId = $(this).attr("data-category-id");
                    var categoryOrder = parseInt($(this).attr("data-order"));
                    orderList.push({
                        "category_id": categoryId,
                        "category_order": categoryOrder
                    });
                }
            });

            $.ajax({
                "url": "{{route('category.order')}}",
                "method": "put",
                "data": {
                    "order": orderList
                },
                "dataType": "json",
                "success": function (response) {
                    if (response.status == false) {
                        alertP("Error", "Unable to update category order, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    alertP("Error", "Unable to update category order, please try again later.");
                }
            })
        }
    </script>
@stop