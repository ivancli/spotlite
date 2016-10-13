<div class="form-group required">
    {!! Form::label('dashboard_name', 'Name', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::text('dashboard_name', null, array('class' => 'form-control')) !!}
    </div>
</div>
@if(isset($orders) && count($orders) > 0)
    <div class="form-group required">
        {!! Form::label('dashboard_order', 'Position', array('class' => 'control-label col-md-3')) !!}
        <div class="col-md-9">
            {!! Form::select('dashboard_order', $orders, null, ['class'=>'form-control']) !!}
        </div>
    </div>
@endif

@if(isset($templates) && count($templates) > 0)
    <div class="form-group required hidden">
        {!! Form::label('dashboard_template_id', 'Template', array('class' => 'control-label col-md-3')) !!}
        <div class="col-md-9">
            {!! Form::select('dashboard_template_id', $templates, null, ['class'=>'form-control']) !!}
        </div>
    </div>
@endif

<div class="form-group">
    <div class="col-md-offset-3 col-md-9">
        <div class="checkbox">
            <label for="is_hidden">
                <input type="checkbox" value="y" name="is_hidden" id="is_hidden"
                        {{isset($dashboard) && $dashboard->is_hidden == 'y' ? 'checked="checked"' : ''}}>
                Hidden
            </label>
        </div>
    </div>
</div>