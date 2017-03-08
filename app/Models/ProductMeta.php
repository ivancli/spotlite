<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMeta extends Model
{
    protected $primaryKey = 'product_meta_id';

    protected $fillable = [
        'brand', 'sku', 'colour', 'size', 'supplier', 'cost_price'
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'product_id');
    }
}
