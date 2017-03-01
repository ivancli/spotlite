<tr class="site-wrapper" data-site-id="{{$site->getKey()}}"
    data-site-edit-url="{{$site->urls['edit']}}"
    data-site-alert-url="{{$site->urls['alert']}}"
    data-site-product-show-url="{{$site->product->urls['show']}}"
    data-site-update-my-price-url="{{$site->urls['update_my_price']}}">
    <td class="site-url vertical-align-middle">
        <a href="{{$site->site_url}}" target="_blank" class="text-muted site-url-link" data-toggle="popover"
           data-container="body"
           data-trigger="hover"
           data-content="{{$site->site_url}}">
            {{parse_url($site->site_url)['host']}}
        </a>
        @if(!auth()->user()->isPastDue)
            <div class="frm-edit-site-url input-group sl-input-group" style="display: none;">
                <input type="text" name="site_url" placeholder="Site URL" autocomplete="off"
                       class="form-control sl-form-control txt-site-url"
                       onkeyup="cancelEditSiteURL(this, event)" onblur="cancelEditSiteURL(this)"
                       value="{{$site->site_url}}">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-flat" data-url="{{$site->urls['update']}}"
                            onclick="getPricesEdit(this); return false;">
                        <i class="fa fa-check"></i>
                    </button>
                </span>
            </div>
            <div class="btn-edit btn-edit-site pull-right">
                <div onclick="toggleEditSiteURL(this)" class="btn-edit-align-middle">
                    Edit &nbsp;
                    <i class="fa fa-pencil-square-o"></i>
                </div>
            </div>
        @endif
    </td>
    @if(!auth()->user()->needSubscription || auth()->user()->subscriptionCriteria()->my_price == true)
        <td align="center">
            <a href="#" class="btn-my-price" style="cursor: default;" onclick="return false;"
               {{--onclick="toggleMyPrice(this); return false;"--}}
               data-product-alert-on-my-price="{{is_null($site->product->alertOnMyPrice()) ? "" : "y"}}"
               data-site-alerts-on-my-price="{{$site->product->siteAlertsOnMyPrice()->count()}}">
                <i class="fa fa-check-circle-o {{$site->my_price == "y" ? "text-primary" : "text-muted-further"}}"></i>
            </a>
        </td>
    @endif
    <td>
        @if($site->status == 'invalid')
            <div class="text-right">
                <a href="#" onclick="return false;" data-toggle="popover" data-trigger="hover focus click"
                   {{--data-content="The site you have provided is not a valid page for pricing. Please update the site with product detail page URL.">--}}
                   data-content="The Product Page URL you have provided does not contain pricing information. Please enter the Product Page URL where the pricing is located.">
                    <i class="fa fa-ban text-danger"></i>
                </a>
                &nbsp;
                Invalid page for pricing
            </div>
        @else
            <div class="text-right">
                @if(is_null($site->recent_price))
                    <div class="p-r-30">
                        <strong><i class="fa fa-minus"></i></strong>
                    </div>
                @else
                    {{"$" . number_format($site->recent_price, 2, '.', ',')}}
                @endif
            </div>
        @endif
    </td>
    <td>
        <div class="text-right">
            @if(!is_null($site->previousPrice) && !is_null($site->diffPrice) && round($site->diffPrice, 2, PHP_ROUND_HALF_UP) != 0)
                ${{number_format($site->previousPrice->price, 2, '.', ',')}}
            @else
                <div class="p-r-30">
                    <strong><i class="fa fa-minus"></i></strong>
                </div>
            @endif
        </div>
    </td>
    <td class="hidden-xs">
        <div class="text-right">
            @if(!is_null($site->diffPrice))
                @if(round($site->diffPrice, 2, PHP_ROUND_HALF_UP) != 0)
                    <i class="glyphicon {{$site->diffPrice > 0 ? "glyphicon-triangle-top text-success" : "glyphicon-triangle-bottom text-danger"}}"></i>
                    ${{number_format(abs($site->diffPrice), 2, '.', ',')}}
                @else
                    <div class="p-r-10">
                        <strong><i class="fa fa-minus"></i></strong>
                    </div>
                @endif
            @else
                <div class="p-r-10">
                    <strong><i class="fa fa-minus"></i></strong>
                </div>
            @endif
        </div>
    </td>
    <td class="hidden-xs" style="padding-left: 20px;">
        @if(!is_null($site->previousPrice) && !is_null($site->diffPrice) && round($site->diffPrice, 2, PHP_ROUND_HALF_UP) != 0 && !is_null($site->priceLastChangedAt))
            {{date(auth()->user()->preference('DATE_FORMAT') . " " . auth()->user()->preference('TIME_FORMAT'), strtotime($site->priceLastChangedAt))}}

        @else
            <div class="p-l-30">
                <strong><i class="fa fa-minus"></i></strong>
            </div>
        @endif
    </td>
    <td>
        @if(!is_null($site->last_crawled_at))
            <span title="{{date(auth()->user()->preference('DATE_FORMAT') . " " . auth()->user()->preference('TIME_FORMAT'), strtotime($site->last_crawled_at))}}"
                  data-toggle="tooltip">
                {{date(auth()->user()->preference('DATE_FORMAT'), strtotime($site->last_crawled_at))}}
                <span class="hidden-xs hidden-sm">{{date(auth()->user()->preference('TIME_FORMAT'), strtotime($site->last_crawled_at))}}</span>
            </span>
        @else
            <div class="p-l-15">
                <strong><i class="fa fa-minus"></i></strong>
            </div>
        @endif
    </td>
    <td>
        <div title="{{date(auth()->user()->preference('DATE_FORMAT') . " " . auth()->user()->preference('TIME_FORMAT'), strtotime($site->created_at))}}"
             data-toggle="tooltip">
            {{date(auth()->user()->preference('DATE_FORMAT'), strtotime($site->created_at))}}
        </div>
    </td>
    <td class="text-right action-cell">
        @if(!auth()->user()->isPastDue)
            <a href="#" class="btn-action" onclick="showSiteChart('{{$site->urls['chart']}}'); return false;"
               data-toggle="tooltip" title="chart">
                <i class="fa fa-line-chart"></i>
            </a>
            {!! Form::model($site, array('route' => array('site.destroy', $site->getKey()), 'method'=>'delete', 'class'=>'frm-delete-site', 'onsubmit' => 'return false;')) !!}
            <a href="#" class="btn-action" data-name="{{parse_url($site->site_url)['host']}}"
               onclick="btnDeleteSiteOnClick(this); return false;"
               data-toggle="tooltip" title="delete">
                <i class="glyphicon glyphicon-trash"></i>
            </a>
            {!! Form::close() !!}
        @endif
    </td>
    <script type="text/javascript">
        $(function () {
            initPopover();
        })
    </script>
</tr>