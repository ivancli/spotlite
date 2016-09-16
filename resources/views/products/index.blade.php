@extends('layouts.adminlte')
@section('title', 'Products')
@section('header_title', "Products")
@section('breadcrumbs')
    {!! Breadcrumbs::render('product_index') !!}
@stop
@section('links')
    <style>
        .list-container .btn-category-dragger {
            -moz-user-select: none;
            -khtml-user-select: none;
            -webkit-user-select: none;
            user-select: none;
            /* Required to make elements draggable in old WebKit */
            -khtml-user-drag: element;
            -webkit-user-drag: element;
        }
    </style>
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


        /**
         * drag and drop source
         */
        var drag_source = null;
        var draggedType = null;

        $(function () {

            /**
             * category drag and drop
             */

            $(".list-container").on("dragstart", ".category-wrapper", function (e) {
                var target = this;
                target.opacity = '0.4';
                drag_source = target;
                console.info('e', e);
                e.originalEvent.dataTransfer.setData('text/html', $("<div>").append($(this).innerHTML).html());
            });
            $(".list-container").on("dragover", ".category-wrapper", function (e) {
                if (e.preventDefault) {
                    e.preventDefault();
                }
                e.dataTransfer.dropEffect = 'move';
                return false;
            });
            $(".list-container").on("dragenter", ".category-wrapper", function (e) {
                $(this).addClass("over");
            });
            $(".list-container").on("dragleave", ".category-wrapper", function (e) {
                $(this).removeClass("over");
            });
            $(".list-container").on("dragend", ".category-wrapper", function (e) {
                $(this).removeClass("over");
            });
            $(".list-container").on("drop", ".category-wrapper", function (e) {
                if (e.stopPropagation) {
                    e.stopPropagation();
                }
                if (drag_source != target) {
                    drag_source.innerHTML = this.innerHTML;
                    target.innerHTML = e.originalEvent.dataTransfer.getData('text/html');
                }
                return false;
            });















            loadCategories(start, initLength, function (response) {
                $(".list-container").append(response.categoriesHTML);
            }, function (xhr, status, error) {

            });
            $(window).scroll(function() {
                if(Math.round($(window).scrollTop() + $(window).height()) == $(document).height()) {
                    if (!theEnd) {
                        loadCategories(start, initLength, function (response) {
                            $(".list-container").append(response.categoriesHTML);
                        }, function (xhr, status, error) {

                        });
                    }
                }
            });
//            window.onscroll = function (ev) {
//                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
//                    if (!theEnd) {
//                        loadCategories(start, initLength, function (response) {
//                            $(".list-container").append(response.categoriesHTML);
//                        }, function (xhr, status, error) {
//
//                        });
//                    }
//                }
//            };
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