@extends('layouts.adminlte')
@section('title', 'Products')

@section('header_title', "Products")


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
    @if(auth()->user()->numberOfLogin <= 3)
        <div class="row">
            <div class="col-sm-12">
                <p class="text-muted font-size-17">
                    You can set up all Categories and Products you want to keep an eye on and SpotLite will update the
                    prices for you automatically. Simply add a Category name, then a Product name. Now all you have to
                    do is
                    copy and paste the Product Page URLs of the prices you want to track on the Product Page URL field
                    within each Product section, as shown below.

                </p>
                <p class="text-muted">
                    Note: you can find the Product Page URLs on your competitors' website, usually on the product
                    details page or where the pricing is located within their website
                </p>
            </div>
        </div>
    @endif

    {{--@include('products.partials.banner_stats')--}}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body product-list-page-content">
                    @if(auth()->user()->isPastDue)
                        <div class="row m-b-20">
                            <div class="col-sm-12" style="background-color: #7ed0c0; padding: 15px;">
                                <table class="table" style="margin: 0; color: white;">
                                    <tr>
                                        <td style="border: none !important;">
                                            <h3 style="margin-top:0;">OH NO! YOUR TRIAL HAS EXPIRED!</h3>
                                            <p style="font-size: 18px;margin-bottom:0">To re-activate your account and
                                                continue to use SpotLite, please add a payment method.</p>
                                        </td>
                                        <td class="shrink"
                                            style="border: none !important; vertical-align: middle !important;">
                                            <a href="{{$updatePaymentLink}}" class="btn btn-primary btn-lg">ADD PAYMENT
                                                METHOD</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="row m-b-10 text-muted font-weight-bold">
                            <div class="col-md-12 line-height-30">
                                @if(auth()->user()->needSubscription && !is_null(auth()->user()->subscription))
                                    @if(!is_null(auth()->user()->apiSubscription->product()))
                                        {{auth()->user()->apiSubscription->product()->name}} Plan:
                                    @else
                                        Credit:
                                    @endif
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    @if(auth()->user()->subscriptionCriteria()->product != 0)
                                        {{--TODO update color based on the ratio--}}
                                        <div class="progress vertical-align-middle"
                                             style="width: 300px;display: inline-block;margin-bottom: 0;background-color:#dedede;border-radius: 10px; height:15px;">
                                            <div class="progress-bar progress-bar-success progress-bar-striped"
                                                 id="prog-product-usage"
                                                 role="progressbar"
                                                 aria-valuenow="{{auth()->user()->products()->count() / auth()->user()->subscriptionCriteria()->product * 100}}"
                                                 aria-valuemin="0" aria-valuemax="100"
                                                 style="width: {{auth()->user()->products()->count() / auth()->user()->subscriptionCriteria()->product * 100}}%">
                                            </div>
                                        </div>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <span id="lbl-product-usage">{{auth()->user()->products()->count()}}</span>
                                        &nbsp;/&nbsp;
                                        <span id="lbl-product-total">{{auth()->user()->subscriptionCriteria()->product == 0 ? "unlimited" : auth()->user()->subscriptionCriteria()->product}}</span>
                                        &nbsp;
                                        products
                                    @else
                                        unlimited products
                                    @endif
                                @endif
                            </div>
                            {{--<div class="col-md-4">--}}
                            {{--<div class="pull-right">--}}
                            {{--<a href="#" onclick="showSetUpNotifications(); return false;"--}}
                            {{--class="btn btn-purple btn-flat {{auth()->user()->isPastDue ? 'disabled' : ''}}" id="btn-set-up-alerts">--}}
                            {{--@if(auth()->user()->alerts()->count() > 0 || auth()->user()->categoryAlerts()->count() > 0 || auth()->user()->productAlerts()->count() > 0)--}}
                            {{--<i class="fa fa-bell ico-alert-enabled"></i>--}}
                            {{--&nbsp;--}}
                            {{--MANAGE ALERTS--}}
                            {{--@else--}}
                            {{--<i class="fa fa-bell-o"></i>--}}
                            {{--&nbsp;--}}
                            {{--SET UP ALERTS--}}
                            {{--@endif--}}
                            {{--</a>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                        </div>

                    @endif
                    <div class="row m-b-10">
                        <div class="col-sm-12" style="position:relative">
                            @if(!auth()->user()->isPastDue)
                                <div class="add-item-block add-category-container"
                                     onclick="appendCreateCategoryBlock(this); event.stopPropagation(); return false;">
                                    <div class="add-item-label">
                                        <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;
                                        <span class="add-item-text">ADD CATEGORY</span>
                                    </div>
                                    <div class="add-item-controls">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <form action="{{route('category.store')}}" method="post"
                                                      class="frm-store-category"
                                                      style="display:inline-block; width: 175px;"
                                                      onsubmit="btnAddCategoryOnClick(this); return false;">
                                                    <input type="text" id="txt-category-name" autocomplete="off"
                                                           class="form-control txt-item" name="category_name">
                                                </form>
                                                <div style="display: inline-block; vertical-align:top;">
                                                    <button class="btn btn-primary btn-flat"
                                                            onclick="btnAddCategoryOnClick(this); event.stopPropagation(); event.preventDefault();">
                                                        CONFIRM
                                                    </button>
                                                    &nbsp;&nbsp;
                                                    <button class="btn btn-default btn-flat"
                                                            id="btn-cancel-add-category"
                                                            onclick="cancelAddCategory(this); event.stopPropagation(); event.preventDefault();">
                                                        CANCEL
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div style="width: auto;padding-top: 30px;position: absolute;right: 15px;top: 0;">
                                <a href="#" onclick="toggleCollapseCategories(this); return false;" class="text-muted"
                                   id="btn-collapse-all" style="font-size: 12px;">Collapse All</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 list-container">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <div class="dotdotdot loading-categories" style="margin: 20px auto; display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript" src="{{elixir('js/product.js')}}"></script>

    <script type="text/javascript">
        var start = 0;
        var length = 5;
        var initLength = 10;
        var theEnd = false;
        var categoryLoadingPromise = false;
        /**
         * drag and drop source
         */
        var drag_source = null;
        var draggedType = null;
        var categoryDrake = null;

        var generalSearchPromise = null;

        $(function () {

            /**
             * category drag and drop
             */
            categoryDrake = dragula([$(".list-container").get(0)], {
                moves: function (el, container, handle) {
                    if ($(".general-search-input").val() != "") {
                        return false;
                    }
                    return $(handle).hasClass("btn-category-dragger") || $(handle).closest(".btn-category-dragger").length > 0;
                }
            }).on('drop', function (el, target, source, sibling) {
                updateCategoryOrder();
            });

            /** enable scrolling when dragging */
            autoScroll([window], {
                margin: 20,
                pixels: 20,
                scrollWhenOutside: true,
                autoScroll: function () {
                    //Only scroll when the pointer is down, and there is a child being dragged.
                    return this.down && categoryDrake.dragging;
                }
            });

            $(".general-search-input").on("input", function () {
                if ($(this).val() != "") {
                    $(".btn-clear-search").fadeIn();
                } else {
                    $(".btn-clear-search").fadeOut();
                }

                if (generalSearchPromise != null) {
                    clearTimeout(generalSearchPromise);
                }
                generalSearchPromise = setTimeout(function () {
                    $(".general-search-input").blur();
                    showLoading();
                    resetFilters();
                    loadCategories(start, initLength, function (response) {
                        $(".list-container").fadeOut(function () {
                            $(".list-container").html(response.categoriesHTML);
                            $(".list-container").fadeIn();
                        });
                        hideLoading();
                        $(".general-search-input").focus();
                        generalSearchPromise = null;
                    }, function (xhr, status, error) {
                        hideLoading();
                        generalSearchPromise = null;
                    });
                }, 500);
            });

            loadCategories(start, initLength, function (response) {
                $(".list-container").append(response.categoriesHTML);
            }, function (xhr, status, error) {

            });
            $(window).scroll(function () {
                if (Math.round($(window).scrollTop() + $(window).height()) == $(document).height()) {
                    if (!theEnd) {
                        loadCategories(start, initLength, function (response) {
                            if ($("#btn-collapse-all").text() == "Expand All") {
                                var $categoriesHTML = $("<div>").append(response.categoriesHTML);
                                $categoriesHTML.find(".btn-collapse").addClass("collapsed").attr({
                                    "aria-expanded": "false"
                                });
                                $categoriesHTML.find(".collapsible-category-div").removeClass("in").attr({
                                    "aria-expanded": "false"
                                });
                                response.categoriesHTML = $categoriesHTML.html();
                            }
                            $(".list-container").append(response.categoriesHTML);
                        }, function (xhr, status, error) {

                        });
                    }
                }
            });

            $(document).on('shown.bs.collapse hidden.bs.collapse', ".collapsible-category-div", function () {
                updateCollapseButtonStatus();
            });
        });

        function clearProductSearch(el) {
            $(".general-search-input").val("");
            $(el).fadeOut();

            resetFilters();
            loadCategories(start, initLength, function (response) {
                $(".list-container").fadeOut(function () {
                    $(".list-container").html(response.categoriesHTML);
                    $(".list-container").fadeIn();
                });
                hideLoading();
                generalSearchPromise = null;
            }, function (xhr, status, error) {
                hideLoading();
                generalSearchPromise = null;
            });
        }

        function appendCreateCategoryBlock(el) {
            $(el).find(".add-item-label").slideUp();
            $(el).find(".add-item-controls").slideDown();
            $("#txt-category-name").focus();
        }

        function cancelAddCategory(el) {
            $(el).closest(".add-item-block").find(".add-item-label").slideDown();
            $(el).closest(".add-item-block").find(".add-item-controls").slideUp();
            $(el).closest(".add-item-block").find(".add-item-controls input").val("");
        }

        function loadCategories(tStart, tLength, successCallback, failCallback) {
            if (categoryLoadingPromise == false) {
                categoryLoadingPromise = true;
                showLoadingCategories();
                $.ajax({
                    "url": "{{route("category.index")}}",
                    "method": "get",
                    "data": {
                        "start": tStart,
                        "length": tLength,
                        "keyword": $(".general-search-input").val()
                    },
                    "dataType": "json",
                    "success": function (response) {
                        categoryLoadingPromise = false;
                        hideLoadingCategories();
                        if (response.status == true) {
                            start += response.recordFiltered;
                            theEnd = response.recordFiltered < tLength;
                            if ($.isFunction(successCallback)) {
                                successCallback(response);
                            }
                        } else {
                            if (typeof response.errors != 'undefined') {
                                var errorMessage = "";
                                $.each(response.errors, function (index, error) {
                                    errorMessage += error + " ";
                                });
                                alertP("Oops! Something went wrong.", errorMessage);
                            } else {
                                alertP("Oops! Something went wrong.", "unable to load categories, please try again later.");
                            }
                        }
                    },
                    "error": function (xhr, status, error) {
                        categoryLoadingPromise = false;
                        hideLoadingCategories();
                        describeServerRespondedError(xhr.status);
                    }
                });
            }
        }


        function btnAddCategoryOnClick() {
            showLoading();
            $.ajax({
                "url": "{{route('category.store')}}",
                "method": "post",
                "data": {
                    "category_name": $("#txt-category-name").val()
                },
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        cancelAddCategory($("#btn-cancel-add-category").get());
                        gaAddCategory();
                        if (response.category != null) {
                            showLoading();
                            loadSingleCategory(response.category.urls.show, function (html) {
                                var $html = $("<div>").append(html);
                                $html.find(".collapsible-category-div").addClass("in").attr("aria-expanded", "true");
                                $html.find(".btn-collapse").removeClass("collapsed").attr("aria-expanded", "true");
                                hideLoading();
                                $(".list-container").prepend($html.html());
                                updateCategoryOrder();
                                updateUserProductCredit();
                            });
                        } else {
                            alertP("Create Category", "Category has been created. But encountered error while page being lodaed.", function () {
                                window.location.reload();
                            });
                        }
                    } else {
                        alertP("Oops! Something went wrong.", 'Unable to add category, please try again later.');
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
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
            })
        }

        function loadSingleCategory(url, callback) {
            $.ajax({
                "url": url,
                "method": "get",
                "success": function (response) {
                    hideLoading();
                    if ($.isFunction(callback)) {
                        callback(response);
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }


        function resetFilters() {
            start = 0;
            length = 5;
        }

        function toggleCollapseCategories(el) {
            var $collapsedDiv = $(".collapsible-category-div").filter(function () {
                return $(this).attr("aria-expanded") == "false";
            });
            if ($collapsedDiv.length != $(".collapsible-category-div").length) {
//                $(".collapsible-category-div").slideUp(function () {
//                    $(this).css({
//                        "height": "0px",
//                        "display": ""
//                    }).attr("aria-expanded", false).removeClass("in");
//                });
//                $(".tbl-category > thead > tr > th > a.btn-collapse").addClass("collapsed").attr("aria-expanded", "false");
                $(".collapsible-category-div").collapse("hide");
                $(".collapsible-product-div").collapse('hide');
                $(el).text("Expand All");
            } else {
//                $(".collapsible-category-div").css("height", "").slideDown(function () {
//                    $(this).css("display", "").attr("aria-expanded", true).addClass("in");
//                });
//                $(".tbl-category > thead > tr > th > a.btn-collapse").removeClass("collapsed").attr("aria-expanded", "true");
                $(".collapsible-category-div").collapse("show");
                $(".collapsible-product-div").collapse('show');
                $(el).text("Collapse All");
            }
        }

        function updateCollapseButtonStatus() {
            var $collapsedDiv = $(".collapsible-category-div").filter(function () {
                return $(this).attr("aria-expanded") == "false";
            });
            if ($collapsedDiv.length != $(".collapsible-category-div").length) {
                $("#btn-collapse-all").text("Collapse All");
            } else {
                $("#btn-collapse-all").text("Expand All");
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
                        if (typeof response.errors != 'undefined') {
                            var errorMessage = "";
                            $.each(response.errors, function (index, error) {
                                errorMessage += error + " ";
                            });
                            alertP("Oops! Something went wrong.", errorMessage);
                        } else {
                            alertP("Oops! Something went wrong.", "Unable to update category order, please try again later.");
                        }
                    } else {
                        gaMoveCategory();
                    }
                },
                "error": function (xhr, status, error) {
                    describeServerRespondedError(xhr.status);
                }
            })
        }

        function hideProductBannerMessage(el) {
            $(el).closest(".callout").slideUp(function () {
                $(this).remove();
            });
            $.ajax({
                "url": "{{route("preference.update", ["element" => "HIDE_PRODUCT_BANNER_MESSAGE", "value" => "1"])}}",
                "method": "put",
                "dataType": "json",
                "success": function (response) {
                },
                "error": function (xhr, status, error) {
                }
            })
        }

        function updateUserProductCredit() {
            if (user.needSubscription) {
                $.ajax({
                    "url": "/product/product/usage",
                    "method": "get",
                    "dataType": "json",
                    "success": function (response) {
                        if (response.status == true) {
                            var total = response.total;
                            var usage = response.usage;

                            $("#prog-product-usage").attr({
                                "aria-valuenow": usage / total * 100
                            }).css("width", (usage / total * 100) + "%");

                            $("#lbl-product-usage").text(usage);
                            $("#lbl-product-total").text(total);
                            updateUserProductUsageBarColor();
                            updateAddProductPanelStatus(usage, total);
                        }
                    },
                    "error": function (xhr, status, error) {
                        describeServerRespondedError(xhr.status);
                    }
                })
            }
        }

        function updateUserProductUsageBarColor() {
            var $progressBar = $("#prog-product-usage")
            var currentValue = $progressBar.attr("aria-valuenow");
            $progressBar.removeClass("progress-bar-warning progress-bar-success, progress-bar-danger");
            if (currentValue < 80) {
                $progressBar.addClass("progress-bar-success");
            } else if (currentValue < 90) {
                $progressBar.addClass("progress-bar-warning");
            } else {
                $progressBar.addClass("progress-bar-danger");
            }
        }

        function updateAddProductPanelStatus(usage, total) {
            if (usage >= total) {
                /*TODO disable add product*/
                $(".add-product-container").attr('onclick', 'appendUpgradeForCreateProductBlock(this); event.stopPropagation(); return false;');
            } else {
                /*TODO enable add product*/
                $(".add-product-container").attr('onclick', 'appendCreateProductBlock(this); event.stopPropagation(); return false;');
            }
        }

        function showSetUpNotifications() {
            showLoading();
            $.ajax({
                "url": "alert/set_up_notifications",
                "method": "get",
                "success": function (html) {
                    hideLoading();
                    var $modal = $(html);
                    $modal.modal();
                    $modal.on("shown.bs.modal", function () {
                        if ($.isFunction(modalReady)) {
                            modalReady({
                                "callback": function (response) {
                                    if ($.isFunction(callback)) {
                                        callback(response);
                                    }
                                }
                            })
                        }
                    });
                    $modal.on("hidden.bs.modal", function () {
                        $("#modal-set-up-notifications").remove();
                    });
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }

        function showLoadingCategories() {
            $(".loading-categories").show();
        }

        function hideLoadingCategories() {
            $(".loading-categories").hide();
        }
    </script>

@stop