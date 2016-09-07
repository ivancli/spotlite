<tr class="site-wrapper" data-product-site-id="{{$site->pivot->product_site_id}}">
    <td>{{parse_url($site->site_url)['host']}}</td>
    <td>{{is_null($site->recent_price) ? '' : "$" . number_format($site->recent_price, 2, '.', ',')}}</td>
    <td></td>
    <td>{{$site->last_crawled_at}}</td>
    <td class="text-right action-cell">
        <a href="#" class="btn-action">
            <i class="fa fa-bell-o"></i>
        </a>
        <a href="#" class="btn-action">
            <i class="fa fa-pencil-square-o"></i>
        </a>

        {!! Form::model($site, array('route' => array('site.destroy', $site->getKey()), 'method'=>'delete', 'class'=>'frm-delete-site', 'onsubmit' => 'return false;')) !!}
        <input type="hidden" name="product_site_id" value="{{$site->pivot->product_site_id}}">
        <a href="#" class="btn-action" onclick="btnDeleteSiteOnClick(this)">
            <i class="glyphicon glyphicon-trash text-danger"></i>
        </a>
        {!! Form::close() !!}
    </td>
    <script type="text/javascript">
        function btnDeleteSiteOnClick(el) {
            confirmP("Delete Site", "Do you want to delete this site?", {
                "affirmative": {
                    "text": "Delete",
                    "class": "btn-danger",
                    "dismiss": true,
                    "callback": function () {
                        var $form = $(el).closest(".frm-delete-site");
                        showLoading();
                        $.ajax({
                            "url": $form.attr("action"),
                            "method": "delete",
                            "data": $form.serialize(),
                            "dataType": "json",
                            "success": function (response) {
                                hideLoading();
                                if (response.status == true) {
                                    alertP("Delete Site", "The site has been deleted.");
                                    $(el).closest(".site-wrapper").remove();
                                } else {
                                    alertP("Error", "Unable to delete site, please try again later.");
                                }
                            },
                            "error": function (xhr, status, error) {
                                hideLoading();
                                alertP("Error", "Unable to delete site, please try again later.");
                            }
                        })
                    }
                },
                "negative": {
                    "text": "Cancel",
                    "class": "btn-default",
                    "dismiss": true
                }
            });
        }
    </script>
</tr>