<div class="box box-widget" data-order="{{$widget->dashboard_widget_order}}" data-id="{{$widget->getKey()}}" style="cursor: move;">
    <div class="box-header with-border">
        <h3 class="box-title">{{$widget->dashboard_widget_name}}</h3>
        @if(!auth()->user()->isPastDue)
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-content="{{
                '<div>'.strtoupper($widget->getPreference('chart_type')) . ' - ' .
                '<strong>' .
                ($widget->getPreference('chart_type') == 'category' ? $widget->category()->category_name : '') .
                ($widget->getPreference('chart_type') == 'product' ? $widget->product()->product_name : '') .
                ($widget->getPreference('chart_type') == 'site' ? parse_url($widget->site()->site_url)['host'] : '') .
                '</strong></div>' .
                "<div>TIMESPAN: <strong>" . (!is_null($widget->dashboard->getPreference('timespan')) ? str_replace('_', ' ', $widget->dashboard->getPreference('timespan')) : str_replace('_', ' ', $widget->getPreference('timespan'))) . "</strong></div>" .
                "<div>PERIOD RESOLUTION: <strong>" . (!is_null($widget->dashboard->getPreference('resolution')) ? $widget->dashboard->getPreference('resolution') : $widget->getPreference('resolution')) . "</strong></div>"
                }}"
                       data-html="true" data-trigger="hover" data-placement="bottom" data-toggle="popover">
                    <i class="fa fa-info"></i>
                </button>

                <div class="btn-group">
                    <a class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-download"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#" onclick="exportChart{{$widget->getKey()}}('png'); return false;">Download PNG</a></li>
                        <li><a href="#" onclick="exportChart{{$widget->getKey()}}('jpeg'); return false;">Download JPEG</a></li>
                        <li><a href="#" onclick="exportChart{{$widget->getKey()}}('pdf'); return false;">Download PDF</a></li>
                        <li><a href="#" onclick="exportChart{{$widget->getKey()}}('svg'); return false;">Download SVG</a></li>
                    </ul>
                </div>


                <button type="button" class="btn btn-box-tool btn-edit-widget" onclick="editWidget(this)" data-url="{{$widget->urls['edit']}}">
                    <i class="fa fa-pencil"></i>
                </button>
                <button type="button" class="btn btn-box-tool" id="btn-delete-widget-{{$widget->getKey()}}"
                        data-url="{{$widget->urls['delete']}}" data-name="{{$widget->dashboard_widget_name}}" onclick="deleteWidget(this)">
                    <i class="fa fa-trash-o"></i>
                </button>
            </div>
        @endif
    </div>
    <div class="box-body">
        <style type="text/css">
            #chart-container-{{$widget->getKey()}} .highcharts-title tspan {
                font-size: 16px;
            }

            #chart-container-{{$widget->getKey()}} .highcharts-container svg {
                overflow: visible;
            }

            #chart-container-{{$widget->getKey()}} .highcharts-container {
                overflow: visible !important;
            }

            #chart-container-{{$widget->getKey()}} .highcharts-container .highcharts-tooltip{
                z-index: 99999;
            }

            #chart-container-{{$widget->getKey()}} .highcharts-container .highcharts-button {
                display: none;
            }
        </style>
        <div id="chart-container-{{$widget->getKey()}}">

        </div>
        {{--TODO check date time in preference and send request back to back-end--}}
        {{--TODO ideally use the same ChartRepository function to generate chart--}}
    </div>
</div>
<script type="text/javascript">
    var widgetChart{{$widget->getKey()}};
</script>
@if($widget->getPreference('chart_type') == 'site')
    <script type="text/javascript">
        $(function () {
            $("[data-toggle=popover]").popover();
            setTimeout(function () {
                widgetChart{{$widget->getKey()}} = new Highcharts.Chart({
                    credits: {
                        enabled: false
                    },
                    lang: {
                        noData: "No side data available!!!"
                    },
                    chart: {
                        renderTo: "chart-container-{{$widget->getKey()}}",
                        events: {
                            load: function () {
                                this.showLoading();
                                this.oldHasData = this.hasData;
                                this.hasData = function () {
                                    return true;
                                };
                            }
                        }
                    },
                    title: {
                        text: '{{ parse_url($widget->site()->site_url)['host']}}'
                    },
                    subtitle: {
                        text: '{!! addslashes(htmlentities($widget->site()->product->product_name)) !!}'
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
                            if (site.average.length == 0) {
                                widgetChart{{$widget->getKey()}} = false;
                            }
                        });
                    }
                },
                "error": function (xhr, status, error) {
                    describeServerRespondedError(xhr.status);
                }
            });
        }

    </script>
@elseif($widget->getPreference('chart_type') == 'product')
    <script type="text/javascript">
        $(function () {
            setTimeout(function () {
                widgetChart{{$widget->getKey()}} = new Highcharts.Chart({
                    credits: {
                        enabled: false
                    },
                    lang: {
                        noData: "No side data available!!!"
                    },
                    chart: {
                        renderTo: "chart-container-{{$widget->getKey()}}",
                        events: {
                            load: function () {
                                this.showLoading();
                                this.oldHasData = this.hasData;
                                this.hasData = function () {
                                    return true;
                                };
                            }
                        }
                    },
                    title: {
                        text: '{!! addslashes(htmlentities($widget->product()->product_name)) !!}'
                    },
                    subtitle: {
                        text: '{!! addslashes(htmlentities($widget->product()->category->category_name)) !!}'
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
                    legend: {},
                    series: []
                });
                loadWidgetChart{{$widget->getKey()}}();
            }, 500);
        });

        function loadWidgetChart{{$widget->getKey()}}() {
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
                    describeServerRespondedError(xhr.status);
                }
            });
        }
    </script>
@elseif($widget->getPreference('chart_type') == 'category')
    <script type="text/javascript">
        $(function () {
            setTimeout(function () {
                widgetChart{{$widget->getKey()}} = new Highcharts.Chart({
                    credits: {
                        enabled: false
                    },
                    lang: {
                        noData: "No side data available!!!"
                    },
                    chart: {
                        renderTo: "chart-container-{{$widget->getKey()}}",
                        events: {
                            load: function () {
                                this.showLoading();
                                this.oldHasData = this.hasData;
                                this.hasData = function () {
                                    return true;
                                };
                            }
                        }
                    },
                    title: {
                        text: '{!! addslashes(htmlentities($widget->category()->category_name)) !!}'
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
                    legend: {},
                    series: []
                });
                loadWidgetChart{{$widget->getKey()}}();
            }, 500);
        });

        function loadWidgetChart{{$widget->getKey()}}() {
            $.ajax({
                "url": "{{$widget->urls['show']}}",
                "method": "get",
                "dataType": "json",
                "success": function (response) {
                    if (typeof response.data != 'undefined') {
                        $.each(response.data, function (productId, product) {

                            var ranInt = Math.random() * 11;
                            widgetChart{{$widget->getKey()}}.addSeries({
                                name: product.name + " Range",
                                data: product.range,
                                type: 'arearange',
                                lineWidth: 0,
                                color: Highcharts.getOptions().colors[Math.floor(ranInt)],
                                fillOpacity: 0.7,
                                zIndex: 0,
                                tooltip: {
                                    pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>${point.low:,.2f} - ${point.high:,.2f}</b><br/>'
                                }
                            });

                            widgetChart{{$widget->getKey()}}.redraw();

                            widgetChart{{$widget->getKey()}}.addSeries({
                                name: product.name + " Average",
                                data: product.average,
                                zIndex: 1,
                                marker: {
                                    lineColor: Highcharts.getOptions().colors[Math.floor(ranInt)]
                                },
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
                    describeServerRespondedError(xhr.status);
                }
            });
        }
    </script>
@endif
<script type="text/javascript">
    function exportChart{{$widget->getKey()}}(type) {
        widgetChart{{$widget->getKey()}}.exportChart({
            type: type
        });
    }
</script>
