<div class="modal fade" tabindex="-1" role="dialog" id="modal-report-task-category">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{$category->category_name}} Category Report</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::model($category->reportTask, array('route' => array('report_task.category.update', $category->getKey()), 'method'=>'put', "onsubmit"=>"return false", "id"=>"frm-category-report-update")) !!}
                <input type="hidden" name="report_task_owner_id" value="{{$category->getKey()}}">
                <input type="hidden" name="report_task_owner_type" value="category">
                <div class="form-group required">
                    {!! Form::label('frequency', 'Frequency', array('class' => 'control-label')) !!}
                    {!! Form::select('frequency', array("daily" => "Daily", "weekly"=>"Weekly", "monthly"=>"Monthly"), null, ['class'=>'form-control sl-form-control', 'onclick'=>'updateSubElements(this)']) !!}
                </div>
                {{--TODO delivery date time--}}

                <div class="form-group show-on-daily">
                    {!! Form::label('weekday_only', 'Weekday Only', array('class' => 'control-label')) !!}
                    {!! Form::checkbox('weekday_only', "y") !!}
                </div>

                <div class="form-group required show-on-daily">
                    {!! Form::label('time', 'Delivery Time', array('class' => 'control-label')) !!}
                    {!! Form::select('time', array(
                    "00:00:00"=>"12:00am",
                    "1:00:00"=>"1:00am",
                    "2:00:00"=>"2:00am",
                    "3:00:00"=>"3:00am",
                    "4:00:00"=>"4:00am",
                    "5:00:00"=>"5:00am",
                    "6:00:00"=>"6:00am",
                    "7:00:00"=>"7:00am",
                    "8:00:00"=>"8:00am",
                    "9:00:00"=>"9:00am",
                    "10:00:00"=>"10:00am",
                    "11:00:00"=>"11:00am",
                    "12:00:00"=>"12:00pm",
                    "13:00:00"=>"1:00pm",
                    "14:00:00"=>"2:00pm",
                    "15:00:00"=>"3:00pm",
                    "16:00:00"=>"4:00pm",
                    "17:00:00"=>"5:00pm",
                    "18:00:00"=>"6:00pm",
                    "19:00:00"=>"7:00pm",
                    "20:00:00"=>"8:00pm",
                    "21:00:00"=>"9:00pm",
                    "22:00:00"=>"10:00pm",
                    "23:00:00"=>"11:00pm",
                    ), null, ['class'=>'form-control sl-form-control']) !!}
                </div>

                <div class="form-group required show-on-weekly" style="display: none;">
                    {!! Form::label('day', 'Delivery Day', array('class' => 'control-label')) !!}
                    {!! Form::select('day', array(
                    "1" => "Monday",
                    "2" => "Tuesday",
                    "3" => "Wednesday",
                    "4" => "Thursday",
                    "5" => "Friday",
                    "6" => "Saturday",
                    "7" => "Sunday",
                    ), null, ['class'=>'form-control sl-form-control']) !!}
                </div>

                <div class="form-group required show-on-monthly" style="display: none;">
                    {!! Form::label('date', 'Delivery Date', array('class' => 'control-label')) !!}
                    {!! Form::select('date', array(
                    "1" => "1",
                    "2" => "2",
                    "3" => "3",
                    "4" => "4",
                    "5" => "5",
                    "6" => "6",
                    "7" => "7",
                    "8" => "8",
                    "9" => "9",
                    "10" => "10",
                    "11" => "11",
                    "12" => "12",
                    "13" => "13",
                    "14" => "14",
                    "15" => "15",
                    "16" => "16",
                    "17" => "17",
                    "18" => "18",
                    "19" => "19",
                    "20" => "20",
                    "21" => "21",
                    "22" => "22",
                    "23" => "23",
                    "24" => "24",
                    "25" => "25",
                    "26" => "26",
                    "27" => "27",
                    "28" => "28",
                    "29" => "29 or last date of the month",
                    "30" => "30 or last date of the month",
                    "31" => "31 or last date of the month",
                    ), null, ['class'=>'form-control sl-form-control']) !!}
                </div>

                <div class="form-group required">
                    {!! Form::label('email[]', 'Add Email', array('class' => 'control-label')) !!}
                    {{--                    {!! Form::select('email[]', $emails, $emails, ['class'=>'form-control', 'multiple' => 'multiple', 'id'=>'sel-email']) !!}--}}
                    {!! Form::select('email[]', [auth()->user()->email], [auth()->user()->email], ['class'=>'form-control', 'multiple' => 'multiple', 'id'=>'sel-email', 'disabled' => 'disabled']) !!}
                    <input type="hidden" name="email[]" value="{{auth()->user()->email}}">
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-update-category-report">OK</button>
                @if(!is_null($category->reportTask))
                    <button class="btn btn-danger" id="btn-delete-category-report">Delete</button>
                @endif
                <button data-dismiss="modal" class="btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#sel-email").select2({
                "tags": true,
                "tokenSeparators": [',', ' ', ';'],
                "placeholder": "Enter Email Address and Press Enter Key"
            });

//            $('input[data-toggle=datepicker]').datepicker({});
            updateSubElements($("#frequency").get(0));

            $("#btn-update-category-report").on("click", function () {
                submitEditReportTask(function (response) {
                    if (response.status == true) {
                        alertP("Create/Update Report Schedule", "Report has been scheduled.");
                        $("#modal-report-task-category").modal("hide");
                        if ($.isFunction(options.updateCallback)) {
                            options.updateCallback(response);
                        }
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#modal-report-task-category .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Error", "Unable to create/update alert, please try again later.");
                        }
                    }
                })
            });

            $("#btn-delete-category-report").on("click", function () {

                confirmP("Delete Report Schedule", "Do you want to delete this report schedule?", {
                    "affirmative": {
                        "text": "Delete",
                        "class": "btn-danger",
                        "dismiss": true,
                        "callback": function () {
                            submitDeleteCategoryReportTask(function (response) {
                                if (response.status == true) {
                                    alertP("Delete Report Schedule", "Report schedule has been deleted.");
                                    $("#modal-report-task-category").modal("hide");
                                    if ($.isFunction(options.deleteCallback)) {
                                        options.deleteCallback(response);
                                    }
                                } else {
                                    if (typeof response.errors != 'undefined') {
                                        var $errorContainer = $("#modal-report-task-category .errors-container");
                                        $errorContainer.empty();
                                        $.each(response.errors, function (index, error) {
                                            $errorContainer.append(
                                                    $("<li>").text(error)
                                            );
                                        });
                                    } else {
                                        alertP("Error", "Unable to delete caetegory report schedule, please try again later.");
                                    }
                                }
                            });
                        }
                    },
                    "negative": {
                        "text": "Cancel",
                        "class": "btn-default",
                        "dismiss": true
                    }
                });
            })
        }

        function submitDeleteCategoryReportTask(callback) {
            showLoading();
            $.ajax({
                "url": "{{route('report_task.category.destroy', $category->getKey())}}",
                "method": "delete",
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if ($.isFunction(callback)) {
                        callback(response);
                    }
                },
                "error": function () {
                    hideLoading();
                    alertP("Error", "Unable to delete category report schedule, please try again later.");
                }
            })
        }

        function updateSubElements(el) {
            switch ($(el).val()) {
                case 'daily':
                    $(".show-on-daily").slideDown();
                    $(".show-on-weekly, .show-on-monthly").slideUp();
                    break;
                case 'weekly':
                    $(".show-on-weekly").slideDown();
                    $(".show-on-daily, .show-on-monthly").slideUp();
                    break;
                case 'monthly':
                    $(".show-on-monthly").slideDown();
                    $(".show-on-daily, .show-on-weekly").slideUp();
                    break;
                default:
            }
        }

        function submitEditReportTask(callback) {
            showLoading();
            $.ajax({
                "url": "{{route('report_task.category.update', $category->getKey())}}",
                "method": "put",
                "data": $("#frm-category-report-update").serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if ($.isFunction(callback)) {
                        callback(response);
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    alertP("Error", "Unable to update product alert, please try again later.");
                }
            })
        }
    </script>
</div>
