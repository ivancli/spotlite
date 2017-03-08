<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_metas', function (Blueprint $table) {
            $table->increments('product_meta_id');
            $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('product_id')->on('products')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->text('brand')->nullable();
            $table->text('sku')->nullable();
            $table->text('colour')->nullable();
            $table->text('size')->nullable();
            $table->text('supplier')->nullable();
            $table->decimal('cost_price')->nullable();
            $table->timestamps();
        });


        $products = \App\Models\Product::all();
        foreach ($products as $product) {
            $product->meta()->save(new \App\Models\ProductMeta);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('product_metas');
    }
}
