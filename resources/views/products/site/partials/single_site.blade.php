<tr class="site-wrapper">
    <td>{{parse_url($site->site_url)['host']}}</td>
    <td>{{$site->recent_price}}</td>
    <td></td>
    <td>{{$site->last_crawled_at}}</td>
    <td class="text-right action-cell">
        <a href="#" class="btn-action">
            <i class="fa fa-bell-o"></i>
        </a>
        <a href="#" class="btn-action">
            <i class="fa fa-pencil-square-o"></i>
        </a>
        <a href="#" class="btn-action">
            <i class="glyphicon glyphicon-trash text-danger"></i>
        </a>
    </td>
</tr>