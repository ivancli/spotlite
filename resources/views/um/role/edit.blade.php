<h3>Edit Role: {{$role->name}}</h3>
<div class="um-form-container">
    @include('um::forms.role.edit')
</div>
<script type="text/javascript">
    $(function () {
        $(".um-form-container select").select2();
    })
</script>
