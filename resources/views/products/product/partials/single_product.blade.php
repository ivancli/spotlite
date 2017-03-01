<table class="table table-condensed product-wrapper" data-product-id="{{$product->getKey()}}"
       data-alert-link="{{$product->urls['alert']}}"
       data-report-task-link="{{$product->urls['report_task']}}"
       data-get-site-usage-per-product-link="{{$product->urls['site_usage']}}">
    <thead>
    <tr>
        <th class="shrink product-th">
            <a class="btn-collapse btn-product-dragger" href="#" onclick="return false;"><i class="fa fa-tag"></i></a>
        </th>
        <th class="product-th">
            <a class="text-muted product-name-link" href="#" onclick="return false;">{{$product->product_name}}</a>
            @if(!auth()->user()->isPastDue)
                {!! Form::model($product, array('route' => array('product.update', $product->getKey()), 'method'=>'delete', 'class'=>'frm-edit-product', 'style' => "display :none;", 'onsubmit' => 'submitEditProductName(this); return false;')) !!}
                <div class="input-group sl-input-group">
                    <input type="text" name="product_name" placeholder="Product Name" autocomplete="off"
                           class="form-control sl-form-control input-lg product-name"
                           onkeyup="cancelEditProductName(this, event)" onblur="txtProductOnBlur(this)"
                           value="{{$product->product_name}}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default btn-flat btn-lg">
                            <i class="fa fa-check"></i>
                        </button>
                    </span>
                </div>
                {!! Form::close() !!}

                <span class="btn-edit btn-edit-product" onclick="toggleEditProductName(this)">Edit &nbsp; <i
                            class="fa fa-pencil-square-o"></i></span>
            @endif
        </th>
        <th class="text-right action-cell product-th">
            @if(!auth()->user()->isPastDue)
                <a href="#" class="btn-action" onclick="showProductChart('{{$product->urls['chart']}}'); return false;"
                   data-toggle="tooltip" title="chart">
                    <i class="fa fa-line-chart"></i>
                </a>
                {{--<a href="#" class="btn-action btn-alert" onclick="showProductAlertForm(this); return false;"--}}
                {{--data-toggle="tooltip" title="alert">--}}
                {{--<i class="fa {{!is_null($product->alert) ? "fa-bell alert-enabled" : "fa-bell-o"}}"></i>--}}
                {{--</a>--}}
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
        <th class="text-center vertical-align-middle" style="background-color: #e8e8e8;padding: 0 !important;"
            width="70">
            <a class="text-muted btn-collapse" style="font-size: 30px;" href="#product-{{$product->getKey()}}"
               role="button"
               data-toggle="collapse" data-parent="#accordion" aria-expanded="true"
               aria-controls="product-{{$product->getKey()}}">
                <i class="fa fa-angle-up"></i>
            </a>
        </th>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <div class="text-light">
                Created
                @if(!is_null($product->created_at))
                    on {{date(auth()->user()->preference('DATE_FORMAT'), strtotime($product->created_at))}}
                @endif
                <strong class="text-muted"><i>by {{$product->user->first_name}} {{$product->user->last_name}}</i></strong>
            </div>
            @if(auth()->user()->needSubscription && !is_null(auth()->user()->subscription) && auth()->user()->subscriptionCriteria()->site != 0)
                <div class="text-light">
                    <small>
                        <strong class="text-muted">
                            <span class="lbl-site-usage-per-product">{{$product->sites()->count()}}</span>
                            /
                            <span class="lbl-site-total-per-product">{{auth()->user()->subscriptionCriteria()->site}}</span>
                        </strong>
                        &nbsp;
                        Product URLs Tracked
                    </small>
                </div>
            @endif
        </td>
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
                        <th width="15%">Site</th>
                        @if(!auth()->user()->needSubscription || auth()->user()->subscriptionCriteria()->my_price == true)
                            <th class="text-center" width="10%">My Site</th>
                        @endif
                        <th width="10%" class="text-right">Current Price</th>
                        <th width="10%" class="text-right">Previous Price</th>
                        <th width="10%" class="hidden-xs text-right">Change</th>
                        <th width="10%" class="hidden-xs" style="padding-left: 20px;">Last Changed</th>
                        <th>Updated</th>
                        <th>Tracked Since</th>
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
                                            <div>ADD THE PRODUCT PAGE URL FOR THE PRICE YOU WANT TO TRACK. E.G.
                                                http://www.company.com.au/productpage/price
                                            </div>
                                            {{--<div>For example http://www.company.com.au/productpage/price</div>--}}
                                        </div>
                                    </div>
                                    <div class="add-item-controls">
                                        <div class="row">
                                            <div class="col-lg-8 col-md-7 col-sm-5 col-xs-4">
                                                <form action="{{route('site.store')}}" method="post"
                                                      class="frm-store-site"
                                                      onsubmit="getPricesCreate(this); return false;">
                                                    <input type="text" autocomplete="off"
                                                           {{--placeholder="e.g. http://www.company.com.au/productpage/price"--}}
                                                           name="site_url"
                                                           class="txt-site-url form-control txt-item">
                                                </form>
                                            </div>
                                            <div class="col-lg-4 col-md-5 col-sm-7 col-xs-8 text-right">
                                                <button class="btn btn-primary btn-flat"
                                                        onclick="getPricesCreate(this); event.stopPropagation(); event.preventDefault();">
                                                    ADD PRODUCT PAGE URL
                                                </button>
                                                &nbsp;&nbsp;
                                                <button class="btn btn-default btn-flat btn-cancel-add-site"
                                                        id="btn-cancel-add-site-{{$product->getKey()}}"
                                                        onclick="cancelAddSite(this); event.stopPropagation(); event.preventDefault();">
                                                    CANCEL
                                                </button>
                                            </div>
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