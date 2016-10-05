<div class="form-group required">
    {!! Form::label('email', 'Email', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::email('email', null, array('class' => 'form-control')) !!}
    </div>
</div>
<div class="form-group required">
    {!! Form::label('password', 'Password', array('class' => 'control-label col-md-3')) !!}
    <div class="col-md-9">
        {!! Form::password('password', array('class' => 'form-control')) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-md-offset-3 col-md-9">
        <div class="checkbox">
            <label for="remember">
                <input type="checkbox" value="1" name="remember" id="remember">
                Remember me
            </label>
        </div>
    </div>
</div>
