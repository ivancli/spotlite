@extends('layouts.adminlte')
@section('title', 'Products')
@section('header_title', "Products")
@section('breadcrumbs')
    {!! Breadcrumbs::render('product_index') !!}
@stop
@section('content')
    @include('products.partials.banner_stats')
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="row m-b-10">
                        <div class="col-sm-12">
                            <a href="#" class="btn btn-primary btn-xs" onclick="appendCreateCategoryBlock();"><i
                                        class="fa fa-plus"></i> Add Category</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 list-container">
                            @foreach($categories as $category)
                                @include('products.category.partials.single_category')
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        function appendCreateCategoryBlock() {
            showLoading();
            var $list = $(".list-container")
            if ($list.find(".category-wrapper.create").length == 0) {
                $.get("{{route('category.create')}}", function (html) {
                    hideLoading();
                    $list.append(html);
                    $list.find(".category-wrapper.create .category-name").focus();
                });
            } else {
                hideLoading();
                $list.find(".category-wrapper.create .category-name").focus();
            }
        }
    </script>
@stop