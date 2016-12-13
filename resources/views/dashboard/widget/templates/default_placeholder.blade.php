<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 12/5/2016
 * Time: 3:21 PM
 */ ?>
<div style="border: 2px dashed #c0c0c0; height: 465px; background-color: #f7f7f7;" class="text-center">
    @if(auth()->user()->categories()->count() > 0)
        <a href="#" class="text-muted" onclick="addWidget(); return false;">
            <div class="vertical-align-middle" style="width: 100%; height: 100%; display: table;">
                <div class="" style="display: table-cell; vertical-align: middle;">
                    <div style="font-weight: bold; font-size: 20px;">
                        Add Chart to Dashboard
                    </div>
                    <h1>
                        <i class="fa fa-plus-circle"></i>
                    </h1>
                </div>
            </div>
        </a>
    @else
        <a href="{{route('product.index')}}" class="text-muted">
            <div class="vertical-align-middle" style="width: 100%; height: 100%; display: table;">
                <div class="" style="display: table-cell; vertical-align: middle;">
                    <div style="font-weight: bold; font-size: 20px;">
                        Add Product to Track
                    </div>
                    <h1>
                        <i class="fa fa-plus-circle"></i>
                    </h1>
                </div>
            </div>
        </a>
    @endif
</div>