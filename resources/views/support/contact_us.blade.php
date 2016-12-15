<div class="modal fade" tabindex="-1" role="dialog" id="modal-contact-us">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Contact Us</h4>
            </div>
            <div class="modal-body">
                <ul class="text-danger errors-container">
                </ul>
                <form action="{{route('contact_us.post')}}" method="post" id="frm-contact-us"
                      onsubmit="submitContactUsForm(this);return false;">
                    <textarea name="comment" class="form-control" rows="5" id="" placeholder="How can we help?"
                              style="resize: vertical;"></textarea>
                </form>
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-primary btn-flat" onclick="submitContactUsForm();return false;">CONFIRM</button>
                <button data-dismiss="modal" class="btn btn-default btn-flat">CANCEL</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function submitContactUsForm(el) {
            var $form = $("#frm-contact-us");
            showLoading();
            $.ajax({
                "url": $form.attr("action"),
                "method": "post",
                "data": $form.serialize(),
                "dataType": "json",
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        alertP("Thanks for contacting us", "Your message has been submitted to our support team. We'll be in touch shortly.", function () {
                            $form.closest(".modal").modal("hide");
                        });
                    } else {
                        alertP("Oops! Something went wrong.", "Unable to send contact message, please try again later.");
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            })
        }
    </script>
</div>
