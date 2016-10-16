<style type="text/css">
    .on-chart-show, .on-category-show, .on-product-show, .on-site-show {
        display: none;
    }
</style>

<div class="form-group required">
    {!! Form::label('dashboard_widget_type_id', 'Content type', array('class' => 'control-label col-md-4')) !!}
    <div class="col-md-8">
        {!! Form::select('dashboard_widget_type_id', $widgetTypes, null, ['class'=>'form-control', 'id' =>'sel-dashboard-widget-type-id', 'onchange' => 'updateFormComponentVisibility(); return false;']) !!}
    </div>
</div>

<div class="on-chart-show">
    {{--Chart options--}}
    <div class="form-group required">
        {!! Form::label('chart_type', 'Chart type', array('class' => 'control-label col-md-4')) !!}
        <div class="col-md-8">
            {!! Form::select('chart_type', array('category' => 'Category', 'product' => 'Product', 'site' => 'Site'),
             (isset($widget) ? $widget->getPreference('chart_type') : null),
              array('class' => 'form-control', 'id'=>'sel-chart-type', 'onchange' => 'updateFormComponentVisibility(); return false;')) !!}
        </div>
    </div>
    <div class="on-category-show">
        <div class="form-group required">
            {!! Form::label('category_id', 'Category', array('class' => 'control-label col-md-4')) !!}
            <div class="col-md-8">
                {!! Form::select('category_id', array(), null, array('class' => 'form-control', 'id' => 'sel-category', 'onchange' => 'categoryOnChange(this); return false;')) !!}
            </div>
        </div>
    </div>

    <div class="on-product-show">
        <div class="form-group required">
            {!! Form::label('product_id', 'Product', array('class' => 'control-label col-md-4')) !!}
            <div class="col-md-8">
                {!! Form::select('product_id', array(), null, array('class' => 'form-control',  'id' => 'sel-product', 'onchange' => 'productOnChange(this); return false;')) !!}
            </div>
        </div>
    </div>

    <div class="on-site-show">
        <div class="form-group required">
            {!! Form::label('site_id', 'Site', array('class' => 'control-label col-md-4')) !!}
            <div class="col-md-8">
                {!! Form::select('site_id', array(), null, array('class' => 'form-control',  'id' => 'sel-site', 'onchange' => 'siteOnChange(this); return false;')) !!}
            </div>
        </div>
    </div>

    <div class="form-group required">
        <label class="col-md-4 control-label">Timespan</label>
        <div class="col-md-8">
            <select id="sel-timespan" name="timespan" class="form-control">
                <option value="this_week">This week</option>
                <option value="last_week">Last week</option>
                <option value="last_7_days">Last 7 days</option>
                <option value="this_month">This month</option>
                <option value="last_month">Last month</option>
                <option value="last_30_days">Last 30 days</option>
                <option value="this_quarter">This quarter</option>
                <option value="last_quarter">Last quarter</option>
                <option value="last_90_days">Last 90 days</option>
            </select>
        </div>
    </div>

    <div class="form-group required">
        <label class="col-md-4 control-label">Period Resolution</label>
        <div class="col-md-8">
            <select id="sel-period-resolution" name="resolution" class="form-control">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>
    </div>
</div>

<div class="form-group required">
    {!! Form::label('dashboard_widget_name', 'Content Name', array('class' => 'control-label col-md-4')) !!}
    <div class="col-md-8">
        {!! Form::text('dashboard_widget_name', null, array('class' => 'form-control')) !!}
    </div>
</div>


@if(isset($categories))
    <script type="text/javascript">
        var categories = {!! json_encode($categories) !!};
    </script>
@endif

@if(isset($widget))
    <script type="text/javascript">
        var widgetPreferences = {!! $widget->preferences->pluck('value', 'element')->toJson() !!};
    </script>
@endif

<script type="text/javascript">
    var $selCategory, $selProduct, $selSite;

    $(function () {
        $selCategory = $("#sel-category");
        $selProduct = $("#sel-product");
        $selSite = $("#sel-site");
        populateSelCategory();


        setTimeout(function () {
            updateFormComponentVisibility();
        }, 300);
    });

    function populateSelCategory() {
        if (typeof categories != 'undefined') {
            $selCategory.empty();
            $.each(categories, function (index, category) {
                $selCategory.append(
                        $("<option>").text(category.category_name).attr({
                            "value": category.category_id
                        })
                )
            })
        }
        populateSelProduct();
    }

    function populateSelProduct() {
        if (typeof categories != 'undefined') {
            var selectedCategories = $.grep(categories, function (element) {
                return element.category_id == $selCategory.val();
            });
            if (selectedCategories.length == 1) {
                var selectedCategory = selectedCategories[0];
                if (typeof selectedCategory.products != 'undefined') {
                    $selProduct.empty();
                    $.each(selectedCategory.products, function (index, product) {
                        $selProduct.append(
                                $("<option>").text(product.product_name).attr({
                                    "value": product.product_id
                                })
                        )
                    })
                }
                populateSelSite();
            }
        }
    }

    function populateSelSite() {
        if (typeof categories != 'undefined') {
            var selectedCategories = $.grep(categories, function (element) {
                return element.category_id == $selCategory.val();
            });
            if (selectedCategories.length == 1) {
                var selectedCategory = selectedCategories[0];
                if (typeof selectedCategory.products != 'undefined') {
                    var selectedProducts = $.grep(selectedCategory.products, function (element) {
                        return element.product_id == $selProduct.val();
                    });
                    if (selectedProducts.length == 1) {
                        var selectedProduct = selectedProducts[0];
                        if (typeof selectedProduct.sites != 'undefined') {
                            $selSite.empty();
                            $.each(selectedProduct.sites, function (index, site) {
                                $selSite.append(
                                        $("<option>").text(site.domain).attr({
                                            "value": site.site_id,
                                            "title": site.site_url
                                        })
                                )
                            })
                        }
                    }
                }
            }
        }
    }

    function categoryOnChange(el) {
        populateSelProduct()
    }

    function productOnChange(el) {
        populateSelSite();
    }

    function siteOnChange(el) {

    }

    function updateFormComponentVisibility() {
        if ($("#sel-dashboard-widget-type-id").val() == 1) {
            $(".on-chart-show").slideDown();
        } else {
            $(".on-chart-show").slideUp();
        }

        switch ($("#sel-chart-type").val()) {
            case "site":
                $(".on-site-show").slideDown();
                $(".on-product-show").slideDown();
                $(".on-category-show").slideDown();
                break;
            case "product":
                $(".on-site-show").slideUp();
                $(".on-product-show").slideDown();
                $(".on-category-show").slideDown();
                break;
            case "category":
                $(".on-site-show").slideUp();
                $(".on-product-show").slideUp();
                $(".on-category-show").slideDown();
                break;
        }
    }

    function timespanOnChange(el) {
        updateShowWhenCustomElements();
    }

    function updateShowWhenCustomElements() {
        if ($("#sel-timespan").val() == "custom") {
            $(".show-when-custom").slideDown();
        } else {
            $(".show-when-custom").slideUp();
        }
    }

</script>