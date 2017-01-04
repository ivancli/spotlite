<div class="modal fade" tabindex="-1" role="dialog" id="modal-set-up-notifications">
    <style type="text/css">
        .lst-category, .lst-product {
            list-style: none;
        }

        .lst-category label,
        .lst-product label {
            font-weight: bold;
        }

        .product-checkbox {
            padding-top: 15px;
            padding-left: 5px;
        }

        .product-checkbox:last-child {
            padding-bottom: 15px;
        }

        .lst-category li,
        .lst-product li {
            position: relative;
        }

        ul.lst-category > li::after,
        ul.lst-category > li::before {
            border: 0;
        }

        .lst-category li::before,
        .lst-product li::before {
            border-left: 1px dashed #c0c0c0;
            bottom: 50px;
            height: 100%;
            top: 0;
            width: 1px;
        }

        .lst-category li::after,
        .lst-product li::after {
            border-top: 1px dashed #c0c0c0;
            height: 20px;
            top: 25px;
            width: 35px;
        }

        .lst-category li::after,
        .lst-category li::before,
        .lst-product li::after,
        .lst-product li::before {
            content: '';
            left: -35px;
            position: absolute;
            right: auto;
        }
    </style>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Set Up Alerts</h4>
            </div>
            <div class="modal-body">
                <div class="warnning-message-container text-danger m-b-10" style="display: none;">
                    <i class="fa fa-info-circle"></i> &nbsp; For this alert to be set up, you need to
                    <a href="https://spotlitehelp.zendesk.com/hc/en-us/articles/235847887-How-do-I-nominate-My-Site-URL-" class="text-danger" style="text-decoration: underline">nominate your site URL</a>
                </div>

                <div class="basic-notifications m-b-20"
                        {{--@if(auth()->user()->categoryAlerts()->count() != 0 || auth()->user()->productAlerts()->count() != 0)--}}
                        {{--style="display: none;"--}}
                        {{--@endif--}}
                >
                    <form id="frm-notification-basic" class="nl-form">
                        Send alert when
                        &nbsp;
                        <select name="notification_type" id="basic-notification-type" onchange="checkCompanyURL();">
                            <option value=""> -- select alert type --</option>
                            <option value="my price" {{!is_null(auth()->user()->alerts()->first()) && auth()->user()->alerts()->first()->comparison_price_type == 'my price' ? "selected" : ""}}>
                                my price was beaten
                            </option>
                            <option value="price changed" {{!is_null(auth()->user()->alerts()->first()) && auth()->user()->alerts()->first()->comparison_price_type == 'price changed' ? "selected" : ""}}>
                                price changes
                            </option>
                        </select>
                        &nbsp;
                        in all categories.
                        <div class="nl-overlay"></div>
                    </form>
                </div>
                <div class="m-b-20">
                    @if(auth()->user()->categoryAlerts()->count() == 0 && auth()->user()->productAlerts()->count() == 0)
                        <a href="#" data-status="basic"
                           onclick="toggleBasicAdvancedNotifications(this); return false;">
                            <i class="fa fa-plus-square-o"></i>&nbsp;&nbsp;advanced alerts
                        </a>
                    @else
                        <a href="#" data-status="advanced"
                           onclick="toggleBasicAdvancedNotifications(this); return false;">
                            <i class="fa fa-plus-square-o"></i>&nbsp;&nbsp;basic alerts
                        </a>
                    @endif
                </div>
                <div class="advanced-notifications"
                     @if(auth()->user()->categoryAlerts()->count() == 0 && auth()->user()->productAlerts()->count() == 0)
                     style="display: none;"
                        @endif
                >
                    @if(!is_null(auth()->user()->subscriptionCriteria()) && auth()->user()->subscriptionCriteria()->alert_report == "basic")
                        <p>
                            Please <a href="{{route('subscription.edit', auth()->user()->subscription->getKey())}}">upgrade
                                your subscription</a> to set up Advanced Alerts
                        </p>
                    @else

                        <p>Enable alert by checking checkboxes next to category / product.</p>
                        <ul class="lst-category text-muted" style="padding-left: 20px;">
                            @foreach($categories as $category)
                                <li class="category-checkbox">
                                    <div class="checkbox form-control-inline" onclick="toggleProductList(this);"
                                         data-category-id="{{$category->getKey()}}">
                                        <label for="">
                                            <input type="checkbox" class="chk-category"
                                                   @if(!is_null($category->alert))
                                                   checked="checked"
                                                   @endif
                                                   onclick="toggleCategoryNotificationForm(this); event.stopPropagation();">
                                            {{$category->category_name}}
                                        </label>
                                    </div>
                                    &nbsp;&nbsp;&nbsp;
                                    <form class="form-control-inline frm-category-notification nl-form"
                                          style="display: none;">
                                        <select class="form-control input-sm form-control-inline sel-category-notification-type"
                                                onchange="checkCompanyURL();">
                                            <option value=""> -- select alert type --</option>
                                            <option value="my price" {{!is_null($category->alert) && $category->alert->comparison_price_type == 'my price' ? "selected" : ""}}>
                                                beats my price
                                            </option>
                                            <option value="price changed" {{!is_null($category->alert) && $category->alert->comparison_price_type == 'price changed' ? "selected" : ""}}>
                                                price changes
                                            </option>
                                        </select>
                                        <div class="nl-overlay"></div>
                                    </form>
                                    <ul class="lst-product"
                                        @if($category->productAlerts()->count() == 0)
                                        style="display: none;"
                                        @endif
                                        id="product-list-{{$category->getKey()}}">
                                        @foreach($category->products as $product)
                                            <li class="product-checkbox">
                                                <div class="checkbox form-control-inline"
                                                     data-product-id="{{$product->getKey()}}">
                                                    <label>
                                                        <input type="checkbox" class="chk-product"
                                                               @if(!is_null($product->alert))
                                                               checked="checked"
                                                               @endif
                                                               onclick="toggleProductNotificationForm(this); event.stopPropagation();">
                                                        {{$product->product_name}}
                                                    </label>
                                                </div>
                                                &nbsp;&nbsp;&nbsp;
                                                <form class="form-control-inline frm-product-notification frm-notification-type nl-form"
                                                      style="display: none;">
                                                    <select class="form-control input-sm form-control-inline sel-notification-type"
                                                            onchange="toggleSpecificPriceInput(this);checkCompanyURL();">
                                                        <option value=""> -- select alert type --</option>
                                                        <option value="price changed" {{!is_null($product->alert) && $product->alert->comparison_price_type == "price changed" ? "selected" : ""}}>
                                                            price changes
                                                        </option>
                                                        <option value="my price" {{!is_null($product->alert) && $product->alert->comparison_price_type == "my price" ? "selected" : ""}}>
                                                            beats my price
                                                        </option>
                                                        <option value="=<" {{!is_null($product->alert) && $product->alert->comparison_price_type == "specific price" ? "selected" : ""}}>
                                                            equal or below a specific price
                                                        </option>
                                                    </select>
                                                    <div class="nl-overlay"></div>
                                                </form>
                                                &nbsp;&nbsp;&nbsp;
                                                <form class="form-control-inline frm-product-notification frm-specific-price nl-form"
                                                      style="display: none;">
                                                    $<input type="text" placeholder="enter a price"
                                                            class="txt-specific-price"
                                                            value="{{!is_null($product->alert) && !is_null($product->alert->comparison_price) ? number_format($product->alert->comparison_price, 2, '.', '') : ""}}"
                                                    >
                                                    <div class="nl-overlay"></div>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="form-group required">
                    {!! Form::label('email[]', 'Email Address', array('class' => 'control-label')) !!}
                    {!! Form::select('email[]', [auth()->user()->email], [auth()->user()->email], ['class'=>'form-control', 'multiple' => 'multiple', 'id'=>'sel-email', 'disabled' => 'disabled']) !!}
                    <input type="hidden" name="email[]" value="{{auth()->user()->email}}" class="txt-email">
                </div>
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" onclick="submitUpdateNotifications(); return false;">CONFIRM
                </button>
                @if(auth()->user()->alerts()->count() > 0 || auth()->user()->categoryAlerts()->count() > 0 || auth()->user()->productAlerts()->count() > 0)
                    <button class="btn btn-danger btn-flat" data-url="{{route('alert.delete_notifications')}}"
                            onclick="deleteAllNotifications(this); return false;">DELETE ALL
                    </button>
                @endif
                <button data-dismiss="modal" class="btn btn-default btn-flat">CANCEL</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#sel-email").select2();

            new NLForm($("#frm-notification-basic").get(0))

            $(".frm-category-notification, .frm-product-notification").each(function () {
                new NLForm(this);
            });
            updateNotificationFormVisibility();
        }

        function toggleProductList(el) {
            var categoryId = $(el).attr("data-category-id");
            var $productList = $("ul#product-list-" + categoryId);
            if ($productList.is(":visible")) {
                $productList.slideUp();
            } else {
                $productList.slideDown();
            }
        }

        function updateNotificationFormVisibility() {
            $(".chk-category").each(function () {
                var $chk = $(this);
                var $li = $chk.closest(".category-checkbox");
                if ($chk.is(":checked")) {
                    $li.find(".frm-category-notification").fadeIn();
                } else {
                    $li.find(".frm-category-notification").fadeOut();
                }
            });

            $(".chk-product").each(function () {
                var $chk = $(this);
                var $li = $chk.closest(".product-checkbox");
                if ($chk.is(":checked")) {
                    $li.find(".frm-notification-type").fadeIn();
                    if ($li.find(".sel-notification-type").val() == "=<") {
                        $li.find(".frm-specific-price").fadeIn();
                    } else {
                        $li.find(".frm-specific-price").fadeOut();
                    }
                } else {
                    $li.find(".frm-notification-type").fadeOut();
                    $li.find(".frm-specific-price").fadeOut();
                }
            });
        }

        function toggleCategoryNotificationForm(el) {
            if ($(el).is(":checked")) {
                $(el).closest("li.category-checkbox").find(".frm-category-notification").fadeIn();
            } else {
                $(el).closest("li.category-checkbox").find(".frm-category-notification").fadeOut();
            }
        }

        function toggleProductNotificationForm(el) {
            if ($(el).is(":checked")) {
                $(el).closest("li.product-checkbox").find(".frm-notification-type").fadeIn();
                if ($(el).closest("li.product-checkbox").find(".frm-notification-type").val() == "=<") {
                    $(el).closest("li.product-checkbox").find(".frm-specific-price").fadeIn();
                }
            } else {
                $(el).closest("li.product-checkbox").find(".frm-product-notification").fadeOut();
            }
        }

        function toggleSpecificPriceInput(el) {
            if ($(el).val() == "=<") {
                $(el).closest(".product-checkbox").find(".frm-specific-price").fadeIn();
            } else {
                $(el).closest(".product-checkbox").find(".frm-specific-price").fadeOut();
            }
        }

        function submitUpdateNotifications(successCallback, failCallback) {
            /*TODO collect data*/
            var data = collectFormData();
            showLoading();
            $.ajax({
                "url": "alert/set_up_notifications",
                "method": "put",
                "data": data,
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if ($.isFunction(successCallback)) {
                        successCallback(response);
                    }
                    $("#modal-set-up-notifications").modal("hide");
                    if (data.notification_type == "" && data.products.length == 0 && data.categories.length == 0) {
                        $("#btn-set-up-alerts").empty().append(
                                $("<i>").addClass("fa fa-bell-o"),
                                " &nbsp; SET UP ALERTS"
                        );
                    } else {
                        $("#btn-set-up-alerts").empty().append(
                                $("<i>").addClass("fa fa-bell ico-alert-enabled"),
                                " &nbsp; MANAGE ALERTS"
                        );
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                    if ($.isFunction(failCallback)) {
                        failCallback(xhr, status, error);
                    }
                }
            });
        }

        function collectFormData() {
            /*TODO category level data*/
            var categoryNotifications = [];
            var productNotifications = [];

            $(".chk-category:checked").each(function () {
                var $categoryList = $(this).closest(".category-checkbox");
                var categoryId = $categoryList.find("[data-category-id]").attr("data-category-id");
                var notificationType = $categoryList.find(".sel-category-notification-type").val();
                categoryNotifications.push({
                    "category_id": categoryId,
                    "type": notificationType
                })
            });

            $(".chk-product:checked").each(function () {
                var $productList = $(this).closest(".product-checkbox");
                var productId = $productList.find("[data-product-id]").attr("data-product-id");
                var notificationType = $productList.find(".sel-notification-type").val();
                var specificPrice = $productList.find(".txt-specific-price").val();
                productNotifications.push({
                    "product_id": productId,
                    "type": notificationType,
                    "specificPrice": specificPrice
                })
            });
            return {
                "notification_type": $("#basic-notification-type").val(),
                "categories": categoryNotifications,
                "products": productNotifications,
                "email[]": $(".txt-email").val()
            }
        }

        function toggleBasicAdvancedNotifications(el) {
            if ($(el).attr("data-status") == 'basic') {
                /*show advanced settings*/
                $(el).attr("data-status", "advanced");
                $(el).empty().append(
                        $("<i>").addClass("fa fa-minus-square-o"),
                        "&nbsp;&nbsp;basic notifications"
                );
//                $(".basic-notifications").slideUp();
                $(".advanced-notifications").slideDown();
            } else {
                $(el).empty().append(
                        $("<i>").addClass("fa fa-plus-square-o"),
                        "&nbsp;&nbsp;advanced notifications"
                );
                $(el).attr("data-status", "basic");
//                $(".basic-notifications").slideDown();
                $(".advanced-notifications").slideUp();
            }
        }

        function checkCompanyURL() {
            $(".warnning-message-container").hide();
            var show = false;
            $(".sel-category-notification-type, .sel-notification-type, #basic-notification-type").each(function () {
                if ($(this).val() == "my price" && (user.company_url == null || user.company_url.trim() == "")) {
                    show = true;
                }
            });
            if (show == true) {
                $(".warnning-message-container").show();
            } else {
                $(".warnning-message-container").hide();
            }
        }

        function deleteAllNotifications(el) {
            deletePopup("Delete Alert", "Are you sure you want to delete all Alerts?",
                    "By deleting all Alerts, you will lose the following:",
                    [
                        "All Alerts set up for this account."
                    ],
                    {
                        "affirmative": {
                            "text": "DELETE",
                            "class": "btn-danger btn-flat",
                            "dismiss": true,
                            "callback": function () {
                                showLoading();
                                $.ajax({
                                    "url": $(el).attr("data-url"),
                                    "method": "delete",
                                    "dataType": "json",
                                    "success": function (response) {
                                        hideLoading();
                                        if (response.status == true) {
                                            $(el).closest(".modal").modal("hide");

                                            $("#btn-set-up-alerts").empty().append(
                                                    $("<i>").addClass("fa fa-bell-o"),
                                                    " &nbsp; SET UP ALERTS"
                                            );
                                        } else {
                                            if (typeof response.errors != 'undefined') {
                                                var errorMessage = "";
                                                $.each(response.errors, function (index, error) {
                                                    errorMessage += error + " ";
                                                });
                                                alertP("Oops! Something went wrong.", errorMessage);
                                            } else {
                                                alertP("Oops! Something went wrong.", "Unable to delete alert, please try again later.");
                                            }
                                        }
                                    },
                                    "error": function (xhr, status, error) {
                                        hideLoading();
                                        describeServerRespondedError(xhr.status);
                                    }
                                })
                            }
                        },
                        "negative": {
                            "text": "CANCEL",
                            "class": "btn-default btn-flat",
                            "dismiss": true
                        }
                    });
        }
    </script>
</div>
