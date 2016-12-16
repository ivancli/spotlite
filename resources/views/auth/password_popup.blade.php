<div class="modal-header" style="border-bottom: 0">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="text-center">
                <img src="{{asset('build/images/logo-fixed-2.png')}}" style="width: 30%;">
            </h3>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 ">
            <div class="jumbotron set-password-form-container">
                <p class="set-password-form-heading">
                    Please set your password.
                </p>

                <ul class="text-danger errors-container" style="padding-left: 8px;">
                </ul>
                <form action="{{route('password.init_reset.post')}}" id="set-password-form" method="post"
                      onsubmit="submitSetPasswordForm(this); return false;">
                    <div class="form-group">
                        <input type="password" name="password" class="form-control"
                               placeholder="Password">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="Confirm password">
                    </div>
                    <div class="form-group text-center">
                        <button class="btn btn-primary btn-flat" type="submit">SET PASSWORD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function submitSetPasswordForm(el) {
            showLoading();
            var $form = $(el);
            $.ajax({
                "url": $form.attr("action"),
                "method": "post",
                "data": $form.serialize(),
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        $form.closest(".modal").modal("hide");
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $(".set-password-form-container .errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Oops! Something went wrong.", "Unable to set password, please try again later.");
                        }
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }
    </script>
</div>