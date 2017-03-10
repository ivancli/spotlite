<ul class="text-danger errors-container">
</ul>
{!! Form::open(array('route' => 'user-domain.store', 'method'=>'post', "id"=>"frm-update-user-domain", "onsubmit"=>"return false;", "class" => "form-horizontal sl-form-horizontal")) !!}
@if(count($domains) > 0)
    @foreach($domains as $domain=>$name)
        <div class="form-group">
            <label for="{{$domain}}" class="control-label col-md-5">{{$domain}}</label>
            <div class="col-md-7">
                <input type="hidden" name="domains[]" value="{{$domain}}">
                <input type="text" name="names[]" class="form-control" id="{{$domain}}" value="{{$name}}">
            </div>
        </div>
    @endforeach

    <div class="text-right">
        {!! Form::submit('UPDATE', ["class"=>"btn btn-primary btn-flat", "href"=>"#", "onclick"=>"submitUpdateUserDomains();"]) !!}
    </div>
@else
    <div class="text-center">
        No sites available, please add Product Page URLs in <a href="{{route('product.index')}}">Products Page</a>.
    </div>
@endif

{!! Form::close() !!}
<script>
    function submitUpdateUserDomains() {
        var $form = $("#frm-update-user-domain");
        $.ajax({
            "url": $form.attr("action"),
            "method": $form.attr("method"),
            "data": $form.serialize(),
            "dataType": "json",
            "success": function (response) {
                if (response.status == true) {
                    alertP("Update Site Names", "Site names have been updated.");
                }
            },
            "error": function (xhr, status, error) {
                hideLoading();
                describeServerRespondedError(xhr.status);
            }
        })
    }
</script>