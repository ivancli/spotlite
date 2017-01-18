<div class="form-group required">
    {!! Form::label('dashboard_name', 'What do you want to call this dashboard?') !!}
    {!! Form::text('dashboard_name', null, array('class' => 'form-control', 'placeholder' => 'Dashboard Name', 'autocomplete' => 'off')) !!}
</div>

@if(isset($templates) && count($templates) > 0)
    <div class="form-group required hidden">
        {!! Form::label('dashboard_template_id', 'Template') !!}
        {!! Form::select('dashboard_template_id', $templates, null, ['class'=>'form-control']) !!}
    </div>
@endif

<div class="checkbox">
    <label for="dashboard_order">
        <input type="checkbox" value="y" name="dashboard_order" id="dashboard_order">
        Do you want this to be <strong>the first dashboard you see when you login</strong>?
    </label>
</div>
