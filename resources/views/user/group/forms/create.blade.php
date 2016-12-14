<ul class="text-danger errors-container">
</ul>

{!! Form::open(array('route' => array('group.store'), 'method'=>'post', 'id'=>'frm-group-store', 'onsubmit' => 'return false;')) !!}
@include('user.group.forms.group')
<div class="text-right">
    {!! Form::submit('Create', ["class"=>"btn btn-primary btn-sm btn-flat", "href" => "#", "onclick" => "groupStoreOnClick()"]) !!}
    <a href="{{route('group.index')}}" class="btn btn-default btn-sm btn-flat">Cancel</a>
</div>
{!! Form::close() !!}

<script type="text/javascript">
    function groupStoreOnClick() {
        showLoading();
        submitGroupStore(function (response) {
            hideLoading();
            if (response.status == true) {
//                alertP("Create Group", "The group has been created.");
                showLoading();
                window.location.href = "{{route('group.index')}}";
            } else {
                if (typeof response.errors != 'undefined') {
                    var $errorContainer = $(".errors-container");
                    $errorContainer.empty();
                    $.each(response.errors, function (index, error) {
                        $errorContainer.append(
                                $("<li>").text(error)
                        );
                    });
                } else {
                    alertP("Oops! Something went wrong.", "Unable to create group, please try again later.");
                }
            }

        }, function (xhr, status, error) {
            hideLoading();
            describeServerRespondedError(xhr.status);
        });
    }

    function submitGroupStore(successCallback, errorCallback) {
        $.ajax({
            "url": $("#frm-group-store").attr("action"),
            "method": "post",
            "data": $("#frm-group-store").serialize(),
            "dataType": "json",
            "success": successCallback,
            "error": errorCallback
        })
    }
</script>