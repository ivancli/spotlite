<ul class="text-danger errors-container">
</ul>

{!! Form::model($group, array('route' => array('group.update', $group->getKey()), 'method'=>'put', 'id'=>'frm-group-update', 'onsubmit'=>'return false;')) !!}
@include('user.group.forms.group')
<div class="text-right">
    {!! Form::submit('Save', ["class"=>"btn btn-primary btn-sm btn-flat", "href"=>"#", "onclick"=>"groupUpdateOnClick()"]) !!}
    <a href="{{route('group.index')}}" class="btn btn-default btn-sm btn-flat">Cancel</a>
</div>
{!! Form::close() !!}

<script type="text/javascript">
    function groupUpdateOnClick() {
        showLoading();
        submitGroupUpdate(function (response) {
            hideLoading();
            if (response.status == true) {
                alertP("Update Group", "The group has been updated.");
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
                    alertP("Error", "Unable to update group, please try again later.");
                }
            }

        }, function (xhr, status, error) {
            hideLoading();
            alertP("Error", "Unable to update group, please try again later.");
        });
    }

    function submitGroupUpdate(successCallback, errorCallback) {
        $.ajax({
            "url": $("#frm-group-update").attr("action"),
            "method": "put",
            "data": $("#frm-group-update").serialize(),
            "dataType": "json",
            "success": successCallback,
            "error": errorCallback
        })
    }
</script>