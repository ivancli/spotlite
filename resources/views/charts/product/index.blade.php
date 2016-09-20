<div class="modal fade" tabindex="-1" role="dialog" id="modal-product-chart">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body" style="background-color: #f5f5f5;">
                <div class="row">
                    <div class="col-lg-5 col-md-6 col-sm-12">
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title">Chart Characteristics</h3>
                            </div>
                            <div class="box-body">
                                <div class="row m-b-10">
                                    <div class="col-sm-12">
                                        <form action="" class="form-horizontal">
                                            <div class="form-group required">
                                                <label class="col-sm-4 control-label">Timespan</label>
                                                <div class="col-sm-8">
                                                    <select name="" id="" class="form-control"></select>
                                                </div>
                                            </div>
                                            <div class="form-group required">
                                                <label class="col-sm-4 control-label">Period Resolution</label>
                                                <div class="col-sm-8">
                                                    <select name="" id="" class="form-control"></select>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button class="btn btn-primary">Generate Chart</button>
                                        <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <div id="chart-container">

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function modalReady() {

            var averages1 = [
                [1246406400000, 1.5],
                [1246492800000, 2.1],
                [1246579200000, 3],
                [1246665600000, 3.8],
                [1246752000000, 1.4],
                [1246838400000, 1.3],
                [1246924800000, 8.3],
                [1247011200000, 5.4],
                [1247097600000, 6.4],
                [1247184000000, 7.7],
                [1247270400000, 7.5],
                [1247356800000, 7.6],
                [1247443200000, 7.7],
                [1247529600000, 6.8],
                [1247616000000, 7.7],
                [1247702400000, 6.3],
                [1247788800000, 7.8],
                [1247875200000, 8.1],
                [1247961600000, 7.2],
                [1248048000000, 4.4],
                [1248134400000, 3.7],
                [1248220800000, 5.7],
                [1248307200000, 4.6],
                [1248393600000, 5.3],
                [1248480000000, 5.3],
                [1248566400000, 5.8],
                [1248652800000, 5.2],
                [1248739200000, 4.8],
                [1248825600000, 4.4],
                [1248912000000, 5],
                [1248998400000, 3.6]
            ], averages2 = [
                [1246406400000, 121.5],
                [1246492800000, 122.1],
                [1246579200000, 123],
                [1246665600000, 123.8],
                [1246752000000, 121.4],
                [1246838400000, 121.3],
                [1246924800000, 118.3],
                [1247011200000, 115.4],
                [1247097600000, 116.4],
                [1247184000000, 117.7],
                [1247270400000, 117.5],
                [1247356800000, 117.6],
                [1247443200000, 117.7],
                [1247529600000, 116.8],
                [1247616000000, 117.7],
                [1247702400000, 116.3],
                [1247788800000, 117.8],
                [1247875200000, 118.1],
                [1247961600000, 117.2],
                [1248048000000, 114.4],
                [1248134400000, 113.7],
                [1248220800000, 115.7],
                [1248307200000, 114.6],
                [1248393600000, 115.3],
                [1248480000000, 115.3],
                [1248566400000, 115.8],
                [1248652800000, 115.2],
                [1248739200000, 114.8],
                [1248825600000, 114.4],
                [1248912000000, 115],
                [1248998400000, 113.6]
            ], averages3 = [
                [1246406400000, 21.5],
                [1246492800000, 22.1],
                [1246579200000, 23],
                [1246665600000, 23.8],
                [1246752000000, 21.4],
                [1246838400000, 21.3],
                [1246924800000, 18.3],
                [1247011200000, 15.4],
                [1247097600000, 16.4],
                [1247184000000, 17.7],
                [1247270400000, 17.5],
                [1247356800000, 17.6],
                [1247443200000, 17.7],
                [1247529600000, 16.8],
                [1247616000000, 17.7],
                [1247702400000, 16.3],
                [1247788800000, 17.8],
                [1247875200000, 18.1],
                [1247961600000, 17.2],
                [1248048000000, 14.4],
                [1248134400000, 13.7],
                [1248220800000, 15.7],
                [1248307200000, 14.6],
                [1248393600000, 15.3],
                [1248480000000, 15.3],
                [1248566400000, 15.8],
                [1248652800000, 15.2],
                [1248739200000, 14.8],
                [1248825600000, 14.4],
                [1248912000000, 15],
                [1248998400000, 13.6]
            ];

            $('#chart-container').highcharts({
                title: {
                    text: 'Monthly Average Temperature',
                    x: -20 //center
                },
                subtitle: {
                    text: 'Source: WorldClimate.com',
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
//                    valueSuffix: '°C',
                    valuePrefix: '$'
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: [{
                    name: 'Tokyo',
                    data: averages1
                }, {
                    name: 'New York',
                    data: averages2
                }, {
                    name: 'Berlin',
                    data: averages3
                }]
            });
        }
    </script>
</div>