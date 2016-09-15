<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-folder-open-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Categories</span>
                <span class="info-box-number">{{isset($categories) ? $categories->count() : 0}}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-shopping-cart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Products</span>
                <span class="info-box-number">{{isset($productCount) ? $productCount : 0}}</span>
            </div>
        </div>
    </div>
    {{--<div class="col-md-3 col-sm-6 col-xs-12">--}}
    {{--<div class="info-box">--}}
    {{--<span class="info-box-icon bg-green"><i class="fa fa-tags"></i></span>--}}
    {{--<div class="info-box-content">--}}
    {{--<span class="info-box-text">Roles</span>--}}
    {{--<span class="info-box-number">{{isset($roleCount) ? $roleCount : 0}}</span>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
</div>