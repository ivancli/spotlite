<table class="table table-hover table-condensed product-wrapper">
    <thead>
    <tr>
        <th class="shrink">
            <a class="btn-collapse" href="#product-{{$product->getKey()}}" role="button" data-toggle="collapse"
               data-parent="#accordion" aria-expanded="true" aria-controls="product-{{$product->getKey()}}">
                <i class="glyphicon glyphicon-menu-hamburger"></i>
            </a>
        </th>
        <th>
            {{$product->product_name}}
            &nbsp;
            <button class="btn btn-primary btn-xs">
                <i class="fa fa-plus"></i> Add Site
            </button>
        </th>
        <th class="text-right action-cell">
            <a href="#" class="btn-action">
                <i class="glyphicon glyphicon-bell"></i>
            </a>
            <a href="#" class="btn-action">
                <i class="glyphicon glyphicon-envelope"></i>
            </a>
            <a href="#" class="btn-action">
                <i class="glyphicon glyphicon-cog"></i>
            </a>
            <a href="#" class="btn-action">
                <i class="glyphicon glyphicon-trash text-danger"></i>
            </a>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
        <td colspan="2" class="table-container">
            <div id="product-{{$product->getKey()}}" class="collapse in" aria-expanded="true">
                <table class="table table-hover table-condensed">
                    <thead>
                    <tr>
                        <th>Site</th>
                        <th>Price</th>
                        <th>My Price</th>
                        <th>Last Update</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--sites here--}}
                    @if(!is_null($product->sites))
                        @foreach($product->sites as $site)
                            @include('products.site.partials.single_site')
                        @endforeach
                    @endif
                    {{--sites here--}}
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>