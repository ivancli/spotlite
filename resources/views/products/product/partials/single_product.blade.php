<table class="table table-condensed product-wrapper" data-product-id="{{$product->getKey()}}"
       data-alert-link="{{$product->urls['alert']}}"
       data-report-task-link="{{$product->urls['report_task']}}"
       data-get-site-usage-per-product-link="{{$product->urls['site_usage']}}"
       data-product-meta-brand="{{$product->meta->brand}}"
       data-product-meta-supplier="{{$product->meta->supplier}}"
       data-product-meta-sku="{{$product->meta->sku}}"
       data-product-meta-cost-price="${{number_format($product->meta->cost_price, 2)}}"
>
    <thead>
    <tr>
        <th class="shrink product-th" style="padding-top: 20px; padding-bottom: 20px;">
            <a class="btn-collapse btn-product-dragger" href="#" onclick="return false;"><i class="fa fa-tag"></i></a>
        </th>
        <th class="product-th">
            <a class="text-muted product-name-link" href="#" onclick="return false;">{{$product->product_name}}</a>
            @if(!auth()->user()->isPastDue)
                {!! Form::model($product, array('route' => array('product.update', $product->getKey()), 'method'=>'delete', 'class'=>'frm-edit-product form-horizontal sl-form-horizontal', 'style' => "display :none;", 'onsubmit' => 'submitEditProductName(this); return false;')) !!}
                <input type="text" name="product_name" autocomplete="off" placeholder="Enter product name" class="form-control txt-item product-name" value="{{$product->product_name}}">
                <div class="form-group">
                    <label class="control-label col-sm-3">Brand</label>
                    <div class="col-sm-9">
                        <input type="text" name="meta[brand]" class="form-control txt-product-meta txt-product-meta-brand" value="{{$product->meta->brand}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">Supplier</label>
                    <div class="col-sm-9">
                        <input type="text" name="meta[supplier]" class="form-control  txt-product-meta txt-product-meta-supplier" value="{{$product->meta->supplier}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">SKU</label>
                    <div class="col-sm-9">
                        <input type="text" name="meta[sku]" class="form-control  txt-product-meta txt-product-meta-sku" value="{{$product->meta->sku}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">Cost price</label>
                    <div class="col-sm-9">
                        <input type="text" name="meta[cost_price]" class="form-control  txt-product-meta txt-product-meta-cost-price" value="{{$product->meta->cost_price}}">
                    </div>
                </div>
                <div class="text-right" style="margin-top: 10px;">
                    <button class="btn btn-primary btn-flat btn-sm"
                            onclick="submitEditProductName(this); event.stopPropagation(); event.preventDefault();">
                        CONFIRM
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-default btn-flat btn-cancel-edit-product btn-sm"
                            onclick="cancelEditProductName(this); event.stopPropagation(); event.preventDefault();">
                        CANCEL
                    </button>
                </div>
                {!! Form::close() !!}

                &nbsp;&nbsp;&nbsp;&nbsp;
                <span class="btn-edit text-muted product-info" id="product-{{$product->getKey()}}-info">
                    <i class="glyphicon glyphicon-info-sign"></i>
                </span>
                &nbsp;
                &nbsp;
                @if($product->cheapestSites->count() > 0)
                    <div style="display:inline-block; font-weight: normal; font-size: 11px;" class="hidden-xs hidden-sm text-muted">
                        Cheapest: <span style="font-weight: bold;">{{$product->cheapestSites->first()->domain}}</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        Current Price: <span style="font-weight: bold;">{{"$" . number_format($product->cheapestSites->first()->recent_price, 2, '.', ',')}}</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        Price Change:
                        <span style="font-weight: bold;">
                            @if(!is_null($product->cheapestSites->first()->diffPrice))
                                @if(round($product->cheapestSites->first()->diffPrice, 2, PHP_ROUND_HALF_UP) != 0)
                                    <i class="glyphicon {{$product->cheapestSites->first()->diffPrice > 0 ? "glyphicon-triangle-top text-increase" : "glyphicon-triangle-bottom text-danger"}}"></i>
                                    ${{number_format(abs($product->cheapestSites->first()->diffPrice), 2, '.', ',')}}
                                @else
                                    <strong><i class="fa fa-minus"></i></strong>
                                @endif
                            @else
                                <strong><i class="fa fa-minus"></i></strong>
                            @endif
                        </span>
                    </div>
                @endif
            @endif
        </th>
        <th class="text-right action-cell product-th" style="padding-bottom: 20px;padding-top: 20px;">
            @if(!auth()->user()->isPastDue)
                <a href="#" class="btn-action btn-edit-product" onclick="toggleEditProductName(this); event.preventDefault(); return false;">
                    <i class="glyphicon glyphicon-pencil"></i>
                </a>
                <a href="#" class="btn-action" onclick="showProductChart('{{$product->urls['chart']}}'); return false;"
                   data-toggle="tooltip" title="chart">
                    <i class="fa fa-line-chart"></i>
                </a>
                <a href="#" class="btn-action" onclick="showProductReportTaskForm(this); return false;"
                   data-toggle="tooltip" title="report">
                    <i class="fa {{!is_null($product->reportTask) ? "fa-envelope ico-report-enabled" : "fa-envelope-o"}}"></i>
                </a>
                {!! Form::model($product, array('route' => array('product.destroy', $product->getKey()), 'method'=>'delete', 'class'=>'frm-delete-product', 'onsubmit' => 'return false;')) !!}
                <a href="#" class="btn-action" data-name="{{$product->product_name}}"
                   onclick="btnDeleteProductOnClick(this); return false;"
                   data-toggle="tooltip" title="delete">
                    <i class="glyphicon glyphicon-trash"></i>
                </a>
                {!! Form::close() !!}
            @endif
        </th>
        <th class="text-center product-th" width="70" style="padding:0 !important">
            <div style="background-color:#e8e8e8; height: 65px;padding-top: 10px; padding-bottom: 10px;">
                <a class="text-muted btn-collapse" style="font-size: 30px;" href="#product-{{$product->getKey()}}"
                   role="button"
                   data-toggle="collapse" data-parent="#accordion" aria-expanded="true"
                   aria-controls="product-{{$product->getKey()}}">
                    <i class="fa fa-angle-up"></i>
                </a>
            </div>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td colspan="3" class="table-container">
            <div id="product-{{$product->getKey()}}" class="collapsible-product-div collapse in m-b-20" aria-expanded="true"
                 data-sites-url="{{$product->urls['show_sites']}}" data-start="0" data-length="10"
            >
                <table class="table table-striped table-condensed tbl-site">
                    <thead>
                    <tr>
                        <th width="15%">Site Name</th>
                        <th class="text-right" width="15%">Current Price</th>
                        <th class="text-right" width="15%">Previous Price</th>
                        <th class="hidden-xs text-right" width="15%">Change</th>
                        <th class="hidden-xs" style="padding-left: 20px;">Last Changed</th>
                        <th width="100px"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="spinner-row" style="display: none;">
                        <td class="text-center" colspan="9">
                            <div class="dotdotdot loading-sites" style="margin: 20px auto;"></div>
                        </td>
                    </tr>
                    <tr class="load-more-site">
                        <td colspan="9">
                            <a class="text-green" style="cursor: pointer"
                               onclick="loadAndAttachSites('{{$product->getKey()}}')">LOAD MORE&hellip;
                            </a>
                        </td>
                    </tr>
                    @if(!auth()->user()->isPastDue)
                        <tr class="add-site-row">
                            <td colspan="9" class="add-item-cell">

                                <div class="add-item-block add-site-container"
                                     @if(auth()->user()->needSubscription && auth()->user()->subscriptionCriteria()->site != 0 && $product->sites()->count() >= auth()->user()->subscriptionCriteria()->site)
                                     onclick="appendUpgradeForCreateSiteBlock(this); event.stopPropagation(); return false;"
                                     @else
                                     onclick="appendCreateSiteBlock(this); event.stopPropagation(); return false;"
                                        @endif
                                >
                                    <div class="add-item-label add-site-label">
                                        <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;
                                        <div class="site-label-text-container">
                                            <div>ADD THE PRODUCT PAGE URL</div>
                                            {{--<div>For example http://www.company.com.au/productpage/price</div>--}}
                                        </div>
                                    </div>
                                    <div class="add-item-controls">
                                        <form action="{{route('site.store')}}" method="post"
                                              class="frm-store-site" style="display: inline-block; width: 175px;"
                                              onsubmit="getPricesCreate(this); return false;">
                                            <input type="text" autocomplete="off" name="site_url" class="txt-site-url form-control txt-item">
                                        </form>
                                        <div style="display:inline-block; vertical-align: top;">
                                            <button class="btn btn-primary btn-flat"
                                                    onclick="getPricesCreate(this); event.stopPropagation(); event.preventDefault();">
                                                CONFIRM
                                            </button>
                                            &nbsp;&nbsp;
                                            <button class="btn btn-default btn-flat btn-cancel-add-site"
                                                    id="btn-cancel-add-site-{{$product->getKey()}}"
                                                    onclick="cancelAddSite(this); event.stopPropagation(); event.preventDefault();">
                                                CANCEL
                                            </button>
                                        </div>
                                    </div>
                                    @if(auth()->user()->needSubscription && !is_null(auth()->user()->subscription) && auth()->user()->subscriptionCriteria()->site != 0)
                                        <div class="upgrade-for-add-item-controls" style="display: none;">
                                            <span class="add-item-text">
                                                You have reached the product URL limit of
                                                {{auth()->user()->apiSubscription->product()->name}} plan.
                                                Please
                                                <a href="{{route('subscription.edit', auth()->user()->subscription->getKey())}}"
                                                   onclick="event.stopPropagation();">upgrade your subscription</a>
                                                to add more products.
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
    <script type="text/javascript">
        var siteDrake{{$product->getKey()}} = null;
        $(function () {
            /**
             * drag and drop
             */
            siteDrake{{$product->getKey()}} = dragula([$("#product-{{$product->getKey()}} > table > tbody").get(0)], {
                moves: function (el, container, handle) {
                    return !$(handle).hasClass("add-site-row") && $(handle).closest(".add-site-row").length == 0 && !$(handle).hasClass("empty-message-row") && $(handle).closest(".empty-message-row").length == 0 && !$(handle).hasClass("load-more-site") && $(handle).closest(".load-more-site").length == 0;
                }
            }).on('drop', function (el, target, source, sibling) {
                updateSiteOrder({{$product->getKey()}});
            });

            loadAndAttachSites('{{$product->getKey()}}');
            $("#product-{{$product->getKey()}}-info").popover({
                content: function () {
                    return $("<div>")
                            .append(function () {
                                console.info($(this));
                                if ($("#product-{{$product->getKey()}}-info").closest(".product-wrapper").attr("data-product-meta-brand")) {
                                    return $("<div>").append(
                                            $("<strong>").text("Brand"),
                                            ": " + $("#product-{{$product->getKey()}}-info").closest(".product-wrapper").attr("data-product-meta-brand"),
                                            $("<br>")
                                    )
                                } else {
                                    return "";
                                }
                            }).append(function () {
                                if ($("#product-{{$product->getKey()}}-info").closest(".product-wrapper").attr("data-product-meta-supplier")) {
                                    return $("<div>").append(
                                            $("<strong>").text("Supplier"),
                                            ": " + $("#product-{{$product->getKey()}}-info").closest(".product-wrapper").attr("data-product-meta-supplier"),
                                            $("<br>")
                                    )
                                } else {
                                    return "";
                                }
                            }).append(function () {
                                if ($("#product-{{$product->getKey()}}-info").closest(".product-wrapper").attr("data-product-meta-sku")) {
                                    return $("<div>").append(
                                            $("<strong>").text("SKU"),
                                            ": " + $("#product-{{$product->getKey()}}-info").closest(".product-wrapper").attr("data-product-meta-sku"),
                                            $("<br>")
                                    )
                                } else {
                                    return "";
                                }
                            }).append(function () {
                                if ($("#product-{{$product->getKey()}}-info").closest(".product-wrapper").attr("data-product-meta-cost-price")) {
                                    return $("<div>").append(
                                            $("<strong>").text("Cost price"),
                                            ": " + $("#product-{{$product->getKey()}}-info").closest(".product-wrapper").attr("data-product-meta-cost-price"),
                                            $("<br>")
                                    )
                                } else {
                                    return "";
                                }
                            }).append(
                                    $("<div>").css("font-size", "12px").append(
                                            "Created by {{auth()->user()->first_name . ' ' . auth()->user()->last_name}} on {{date(auth()->user()->preference('DATE_FORMAT'), strtotime($product->created_at))}}"
                                    )
                                    @if(auth()->user()->needSubscription && !is_null(auth()->user()->subscription) && auth()->user()->subscriptionCriteria()->site != 0)
                                    ,
                                    $("<div>").css("font-size", "12px").append(
                                            "{{$product->sites()->count()}}/{{auth()->user()->subscriptionCriteria()->site}} Product URLs Tracked"
                                    )
                                    @endif
                             ).html()
                },
                html: true,
                trigger: "hover"
            })
        });

        function loadAndAttachSites(product_id) {
            loadSites(product_id, function (response) {
                $("#product-" + product_id + " .tbl-site tbody .spinner-row").before(response.html);
                updateProductEmptyMessage();
            });
        }

        function loadSites(product_id, successCallback, failCallback) {
            showLoadingSites(product_id);
            var $productWrapper = $("#product-" + product_id);
            $productWrapper.find(".load-more-site").hide();
            $.ajax({
                "url": $productWrapper.attr("data-sites-url"),
                "data": {
                    "start": $productWrapper.attr("data-start"),
                    "length": $productWrapper.attr("data-length"),
                    "keyword": $(".general-search-input").val()
                },
                "dataType": "json",
                "success": function (response) {
                    hideLoadingSites(product_id);
                    if (response.status == true) {
                        var loadedSitesCount = $("<div>").append(response.html).find("tr").length;
                        $productWrapper.attr("data-end", loadedSitesCount < $productWrapper.attr("data-length") ? "true" : "false");
                        $productWrapper.attr("data-start", parseInt($productWrapper.attr("data-start")) + loadedSitesCount);
                        if (loadedSitesCount < $productWrapper.attr("data-length")) {
                            $productWrapper.find(".load-more-site").remove();
                        } else {
                            $productWrapper.find(".load-more-site").show();
                        }
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
                            alertP("Oops! Something went wrong.", "unable to load products, please try again later.");
                        }
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoadingSites(product_id);
                    describeServerRespondedError(xhr.status);
                    if ($.isFunction(failCallback)) {
                        failCallback(xhr, status, error);
                    }
                }
            })
        }

        function showLoadingSites(product_id) {
            $("#product-" + product_id).find(".spinner-row").show();
        }

        function hideLoadingSites(product_id) {
            $("#product-" + product_id).find(".spinner-row").hide();
        }
    </script>
</table>