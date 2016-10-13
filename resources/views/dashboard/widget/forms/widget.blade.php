<div class="form-group required">
    {!! Form::label('dashboard_widget_type_id', 'Widget type', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::select('dashboard_order', $widgetTypes, null, ['class'=>'form-control']) !!}
    </div>
</div>


{{--Chart options--}}
<div class="form-group required on-chart-show">
    {!! Form::label('chart_type', 'Chart type', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::select('chart_type', array('site' => 'Site', 'product' => 'Product', 'category' => 'Category'), null, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group required on-chart-show">
    {!! Form::label('category_id', 'Category', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::select('category_id', array(), null, array('class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group required on-chart-show">
    {!! Form::label('product_id', 'Product', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::select('product_id', array(), null, array('class' => 'form-control')) !!}
    </div>
</div>
<div class="form-group required on-chart-show">
    {!! Form::label('site_id', 'Site', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::select('site_id', array(), null, array('class' => 'form-control')) !!}
    </div>
</div>


<div class="form-group required">
    <label class="col-sm-3 control-label">Timespan</label>
    <div class="col-sm-9">
        <select id="sel-timespan" name="timespan" class="form-control"
                onchange="timespanOnChange(this)">
            <option value="this_week">This week</option>
            <option value="last_week">Last week</option>
            <option value="last_7_days">Last 7 days</option>
            <option value="this_month">This month</option>
            <option value="last_month">Last month</option>
            <option value="last_30_days">Last 30 days</option>
            <option value="this_quarter">This quarter</option>
            <option value="last_quarter">Last quarter</option>
            <option value="last_90_days">Last 90 days</option>
            <option value="custom">Custom</option>
        </select>
    </div>
</div>
<div class="form-group show-when-custom" style="display: none;">
    <label class="col-sm-3 control-label">Date range:</label>

    <div class="col-sm-9">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control pull-right" name="date_range" id="txt-date-range"
                   readonly="readonly">
            <input type="hidden" name="start_date" id="txt-start-date">
            <input type="hidden" name="end_date" id="txt-end-date">
        </div>
    </div>
</div>


<div class="form-group required">
    <label class="col-sm-4 control-label">Period Resolution</label>
    <div class="col-sm-8">
        <select id="sel-period-resolution" name="resolution" class="form-control">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
        </select>
    </div>
</div>

<script>

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

    function setStartEndDate() {
        var startDate = null;
        var endDate = null;
        switch ($("#sel-timespan").val()) {
            case "this_week":
                startDate = moment().startOf('isoweek').format("X");
                endDate = moment().format("X");
                break;
            case "last_week":
                startDate = moment().subtract(1, 'week').startOf('isoweek').format("X");
                endDate = moment().subtract(1, 'week').endOf('isoweek').format("X");
                break;
            case "last_7_days":
                startDate = moment().subtract(7, 'day').format("X");
                endDate = moment().format("X");
                break;
            case "this_month":
                startDate = moment().startOf("month").format("X");
                endDate = moment().format("X");
                break;
            case "last_month":
                startDate = moment().subtract(1, 'month').startOf("month").format("X");
                endDate = moment().subtract(1, 'month').endOf("month").format("X");
                break;
            case "last_30_days":
                startDate = moment().subtract(30, 'day').format("X");
                endDate = moment().format("X");
                break;
            case "this_quarter":
                startDate = moment().startOf("quarter").format("X");
                endDate = moment().format("X");
                break;
            case "last_quarter":
                startDate = moment().subtract(1, 'quarter').startOf("quarter").format("X");
                endDate = moment().subtract(1, 'quarter').endOf("quarter").format("X");
                break;
            case "last_90_days":
                startDate = moment().subtract(90, 'day').format("X");
                endDate = moment().format("X");
                break;
            case "custom":
            default:
                startDate = $("#txt-start-date").val();
                endDate = $("#txt-end-date").val();
        }

        if (startDate == null || endDate == null) {
            alertP("Error", "Please select the start date and end date for the timespan.");
            return false;
        }

        $("#txt-start-date").val(startDate);
        $("#txt-end-date").val(endDate);
    }

</script>