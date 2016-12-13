<div class="row category-wrapper" data-category-id="{{$category->getKey()}}" draggable="true"
     data-report-task-link="{{$category->urls['report_task']}}"
     data-get-site-usage-link="{{$category->urls['site_usage']}}"
>
    <div class="col-sm-12">
        <table class="table table-condensed tbl-category">
            <thead>
            <tr>
                <th class="shrink category-th">
                    <a class="btn-collapse btn-category-dragger"
                       {{--href="#category-{{$category->getKey()}}" role="button"--}}
                       {{--data-toggle="collapse" data-parent="#accordion" aria-expanded="true"--}}
                       {{--aria-controls="category-{{$category->getKey()}}"--}}
                    >
                        <i class="fa fa-tag "></i>
                    </a>
                </th>
                <th class="category-th">
                    <a class="text-muted category-name-link" href="#" onclick="return false;"
                            {{--href="#category-{{$category->getKey()}}" role="button"--}}
                            {{--data-toggle="collapse" data-parent="#accordion" aria-expanded="true"--}}
                            {{--aria-controls="category-{{$category->getKey()}}"--}}
                    >{{$category->category_name}}</a>


                    {!! Form::model($category, array('route' => array('category.update', $category->getKey()), 'method'=>'delete', 'class'=>'frm-edit-category', 'onsubmit' => 'submitEditCategoryName(this); return false;', 'style' => 'display: none;')) !!}
                    <div class="input-group sl-input-group">
                        <input type="text" name="category_name" placeholder="Category Name"
                               class="form-control sl-form-control input-lg category-name"
                               value="{{$category->category_name}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default btn-flat btn-lg">
                                <i class="fa fa-pencil"></i>
                            </button>
                        </span>
                    </div>
                    {!! Form::close() !!}

                    <span class="btn-edit btn-edit-category" onclick="toggleEditCategoryName(this)">Edit &nbsp; <i
                                class="fa fa-pencil-square-o"></i></span>
                </th>

                <th class="text-right action-cell category-th">
                    <a href="#" class="btn-action btn-chart" data-toggle="tooltip" title="chart"
                       onclick="showCategoryChart('{{$category->urls['chart']}}'); return false;">
                        <i class="fa fa-line-chart"></i>
                    </a>
                    <a href="#" class="btn-action btn-report" onclick="showCategoryReportTaskForm(this); return false;"
                       data-toggle="tooltip"
                       title="report">
                        <i class="fa {{!is_null($category->reportTask) ? "fa-envelope text-success" : "fa-envelope-o"}}"></i>
                    </a>
                    {!! Form::model($category, array('route' => array('category.destroy', $category->getKey()), 'method'=>'delete', 'class'=>'frm-delete-category', 'onsubmit' => 'return false;')) !!}
                    <a href="#" data-name="{{$category->category_name}}" class="btn-action"
                       onclick="btnDeleteCategoryOnClick(this); return false;" data-toggle="tooltip"
                       title="delete">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                    {!! Form::close() !!}
                </th>
                <th class="text-center vertical-align-middle" style="background-color: #d3d3d3;" width="70">
                    <a class="text-muted btn-collapse" style="font-size: 35px;" href="#category-{{$category->getKey()}}"
                       role="button"
                       data-toggle="collapse" data-parent="#accordion" aria-expanded="true"
                       aria-controls="category-{{$category->getKey()}}">
                        <i class="fa fa-angle-up"></i>
                    </a>
                </th>
            </tr>
            <tr>
                <th></th>
                <td colspan="3" class="category-th">
                    <div class="text-light">
                        Created
                        @if(!is_null($category->created_at))
                            on {{date(auth()->user()->preference('DATE_FORMAT'), strtotime($category->created_at))}}
                        @endif
                        <strong class="text-muted"><i>by {{$category->user->first_name}} {{$category->user->last_name}}</i></strong>
                    </div>
                    <div class="text-light">
                        Product URLs Tracked:
                        <strong><span class="lbl-site-usage text-muted">{{$category->sites()->count()}}</span></strong>
                    </div>
                </td>
            </tr>
            <tr>
                <th></th>
                <th colspan="3" class="category-th action-cell add-item-cell">
                    <div class="add-item-block add-product-container"
                         onclick="appendCreateProductBlock(this); event.stopPropagation(); return false;">
                        <div class="add-item-label">
                            <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;
                            <span class="add-item-text">ADD PRODUCT</span>
                        </div>
                        <div class="add-item-controls">
                            <div class="row">
                                <div class="col-lg-8 col-md-7 col-sm-5 col-xs-4">
                                    <form action="{{route('product.store')}}" method="post"
                                          class="frm-store-product"
                                          onsubmit="btnAddProductOnClick(this); return false;">
                                        <input type="text" placeholder="Product Name" name="product_name"
                                               class="form-control txt-item txt-product-name">
                                    </form>
                                </div>
                                <div class="col-lg-4 col-md-5 col-sm-7 col-xs-8 text-right">
                                    <button class="btn btn-primary"
                                            onclick="btnAddProductOnClick(this); event.stopPropagation(); event.preventDefault();">
                                        ADD PRODUCT
                                    </button>
                                    &nbsp;&nbsp;
                                    <button class="btn btn-default btn-cancel-add-product"
                                            onclick="cancelAddProduct(this); event.stopPropagation(); event.preventDefault();">
                                        CANCEL
                                    </button>
                                </div>
                            </div>
                        </div>
                        @if(!auth()->user()->isStaff)
                            <div class="upgrade-for-add-item-controls" style="display: none;">
                            <span class="add-item-text">
                                You have reached the product limit of
                                {{auth()->user()->apiSubscription->product()->name}} plan.
                                Please
                                <a href="{{route('subscription.edit', auth()->user()->subscription->getKey())}}"
                                   onclick="event.stopPropagation();">
                                    upgrade your subscription
                                </a> to add more products.
                            </span>
                            </div>
                        @endif
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td colspan="3" class="table-container">
                    <div id="category-{{$category->getKey()}}" class="collapse in collapsible-category-div"
                         aria-expanded="true">
                        @if($category->products()->count() > 0)
                            @if(request()->has('keyword') && !empty(request()->get('keyword')) && strpos(strtolower($category->category_name), strtolower(request()->get('keyword'))) === FALSE)
                                @foreach($category->filteredProducts()->orderBy('product_order')->orderBy('product_id')->get() as $product)
                                    @include('products.product.partials.single_product')
                                @endforeach
                            @else
                                @foreach($category->products()->orderBy('product_order')->orderBy('product_id')->get() as $product)
                                    @include('products.product.partials.single_product')
                                @endforeach
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <script type="text/javascript">

        var productDrake{{$category->getKey()}} = null;

        $(function () {

            productDrake{{$category->getKey()}} = dragula([$("#category-{{$category->getKey()}}").get(0)], {
                moves: function (el, container, handle) {
                    return $(handle).hasClass("btn-product-dragger") || $(handle).closest(".btn-product-dragger").length > 0;
                }
            }).on('drop', function (el, target, source, sibling) {
                updateProductOrder({{$category->getKey()}});
            });


            /** enable scrolling when dragging*/
            autoScroll([window], {
                margin: 20,
                pixels: 20,
                scrollWhenOutside: true,
                autoScroll: function () {
                    //Only scroll when the pointer is down, and there is a child being dragged.
                    return this.down && productDrake{{$category->getKey()}}.dragging;
                }
            });
        });

    </script>
</div>