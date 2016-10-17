<div class="modal fade" tabindex="-1" role="dialog" id="modal-dashboard-filter-update">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Apply Filters</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::model($dashboard, array('route' => array('dashboard.filter.update', $dashboard->getKey()), 'method'=>'put', "onsubmit"=>"return false", "class" => "form-horizontal sl-form-horizontal", "id"=>"frm-dashboard-filter-update")) !!}

                <div class="form-group required">
                    <label class="col-md-4 control-label">Timespan</label>
                    <div class="col-md-8">
                        <select id="sel-timespan" name="timespan" class="form-control">
                            <option value="this_week" {{$dashboard->getPreference('timespan') == 'this_week' ? 'selected' : ''}}>
                                This week
                            </option>
                            <option value="last_week" {{$dashboard->getPreference('timespan') == 'last_week' ? 'selected' : ''}}>
                                Last week
                            </option>
                            <option value="last_7_days" {{$dashboard->getPreference('timespan') == 'last_7_days' ? 'selected' : ''}}>
                                Last 7 days
                            </option>
                            <option value="this_month" {{$dashboard->getPreference('timespan') == 'this_month' ? 'selected' : ''}}>
                                This month
                            </option>
                            <option value="last_month" {{$dashboard->getPreference('timespan') == 'last_month' ? 'selected' : ''}}>
                                Last month
                            </option>
                            <option value="last_30_days" {{$dashboard->getPreference('timespan') == 'last_30_days' ? 'selected' : ''}}>
                                Last 30 days
                            </option>
                            <option value="this_quarter" {{$dashboard->getPreference('timespan') == 'this_quarter' ? 'selected' : ''}}>
                                This quarter
                            </option>
                            <option value="last_quarter" {{$dashboard->getPreference('timespan') == 'last_quarter' ? 'selected' : ''}}>
                                Last quarter
                            </option>
                            <option value="last_90_days" {{$dashboard->getPreference('timespan') == 'last_90_days' ? 'selected' : ''}}>
                                Last 90 days
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group required">
                    <label class="col-md-4 control-label">Period Resolution</label>
                    <div class="col-md-8">
                        <select id="sel-period-resolution" name="resolution" class="form-control">
                            <option value="daily" {{$dashboard->getPreference('resolution') == 'daily' ? 'selected' : ''}}>
                                Daily
                            </option>
                            <option value="weekly" {{$dashboard->getPreference('resolution') == 'weekly' ? 'selected' : ''}}>
                                Weekly
                            </option>
                            <option value="monthly" {{$dashboard->getPreference('resolution') == 'monthly' ? 'selected' : ''}}>
                                Monthly
                            </option>
                        </select>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-update-dashboard-filter">OK</button>
                <button class="btn btn-default" id="btn-reset-dashboard-filter">Reset</button>
                <button data-dismiss="modal" class="btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#btn-update-dashboard-filter").on("click", function () {
                submitUpdateFilters(function (response) {
                    if ($.isFunction(options.callback)) {
                        options.callback(response);
                    }
                });
            });

            $("#btn-reset-dashboard-filter").on("click", function () {
                submitResetFilters(function (response) {
                    if ($.isFunction(options.callback)) {
                        options.callback(response);
                    }
                })
            })
        }

        function submitUpdateFilters(callback) {
            showLoading();
            $.ajax({
                "url": $("#frm-dashboard-filter-update").attr("action"),
                "method": "put",
                "data": $("#frm-dashboard-filter-update").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        if ($.isFunction(callback)) {
                            callback();
                        }
                        $("#modal-dashboard-filter-update").modal("hide");
                    } else {
                        alertP('Error', 'Unable to update filters, please try again later.');
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to update filters, please try again later.");
                }
            })
        }

        function submitResetFilters(callback) {
            showLoading();
            $.ajax({
                "url": "{{route('dashboard.filter.destroy', $dashboard->getKey())}}",
                "method": "delete",
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        if ($.isFunction(callback)) {
                            callback();
                        }
                        $("#modal-dashboard-filter-update").modal("hide");
                    } else {
                        alertP('Error', 'Unable to update filters, please try again later.');
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to update filters, please try again later.");
                }
            })
        }
    </script>
</div>
