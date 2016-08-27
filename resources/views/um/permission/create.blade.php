<h3>Create Permission</h3>
<div class="um-form-container">
    @include('um::forms.permission.create')
</div>
<script type="text/javascript">
    $(function () {
        $(".um-form-container select").select2();
    })
</script>
