<div class="modal fade" tabindex="-1" role="dialog" id="modal-dashboard-widget-store">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Content</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                {!! Form::open(array('route' => array('dashboard.widget.store'), 'method'=>'post', "onsubmit"=>"return false", "class" => "form-horizontal sl-form-horizontal", "id"=>"frm-dashboard-widget-store")) !!}
                @include('dashboard.widget.forms.widget')
                {!! Form::close() !!}
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary" id="btn-create-dashboard-widget">Add Chart</button>
                <button data-dismiss="modal" class="btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function modalReady(options) {
            $("#txt-date-range").daterangepicker({
                "maxDate": moment()
            }).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                $("#txt-category-chart-start-date").val(picker.startDate.format('X'));
                $("#txt-category-chart-end-date").val(picker.endDate.format('X'));
            });

            $("#btn-create-dashboard-widget").on("click", function () {
                setStartEndDate();
            });
        }

    </script>
</div>
