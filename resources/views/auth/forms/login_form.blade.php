<div class="form-group has-feedback">
    {!! Form::email('email', null, array('class' => 'form-control', 'placeholder' => 'Email')) !!}
    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
</div>
<div class="form-group has-feedback">
    {!! Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password')) !!}
    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
</div>

<div class="row">
    <div class="col-sm-8">
        <div class="checkbox icheck">
            <label>
                <input type="checkbox" value="1" name="remember" id="remember"> &nbsp; Remember Me
            </label>
        </div>
    </div>
    <div class="col-sm-4">
        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
    </div>
</div>
