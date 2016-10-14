<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">{{$widget->dashboard_widget_name}}</h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool">
                <i class="fa fa-cog"></i>
            </button>
            <button type="button" class="btn btn-box-tool" id="btn-delete-widget-{{$widget->getKey()}}"
                    data-url="{{$widget->urls['delete']}}" onclick="deleteWidget(this)">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div id="chart-container-{{$widget->getKey()}}">

        </div>
        {{--TODO check date time in preference and send request back to back-end--}}
        {{--TODO ideally use the same ChartRepository function to generate chart--}}
    </div>
</div>
@if($widget->getPreference('chart_type') == 'site')
    <script type="text/javascript">
        var widgetChart{{$widget->getKey()}};
        $(function () {
            setTimeout(function () {
                widgetChart{{$widget->getKey()}} = new Highcharts.Chart({
                    credits: {
                        enabled: false
                    },
                    lang:{
                        noData: "No side data available!!!"
                    },
                    chart: {
                        renderTo: "chart-container-{{$widget->getKey()}}"
                    },
                    title: {
                        text: '{{parse_url($widget->site()->site_url)['host']}}',
                        x: -20
                    },
                    subtitle: {
                        text: '{{$widget->site()->product->product_name}}',
                        x: -20
                    },
                    xAxis: {
                        type: "datetime"
                    },
                    yAxis: {
                        title: {
                            text: '$ Price'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    tooltip: {
                        valuePrefix: '$'
                    },
                    series: []
                });
                loadWidgetChart{{$widget->getKey()}}();
            }, 500);
        });

        function loadWidgetChart{{$widget->getKey()}}() {
            widgetChart{{$widget->getKey()}}.showLoading("Loading data...");
            $.ajax({
                "url": "{{$widget->urls['show']}}",
                "method": "get",
                "dataType": "json",
                "success": function (response) {
                    if (typeof response.data != 'undefined') {
                        $.each(response.data, function (index, site) {
                            widgetChart{{$widget->getKey()}}.addSeries({
                                name: site.name + " Average",
                                data: site.average,
                                tooltip: {
                                    pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>${point.y:,.2f}</b><br/>'
                                }
                            });

                            widgetChart{{$widget->getKey()}}.redraw();
                            widgetChart{{$widget->getKey()}}.hideLoading();
                        });
                    }
                },
                "error": function (xhr, status, error) {

                }
            });
        }
    </script>
@endif
