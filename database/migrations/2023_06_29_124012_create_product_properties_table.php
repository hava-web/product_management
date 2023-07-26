<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('size_id');
            $table->unsignedBigInteger('agent_id');
            $table->date('expired_date');
            $table->string('status');
            $table->decimal('original_price',12,2);
            $table->decimal('selling_price',12,2);
            $table->tinyInteger('sale_percentage');
            $table->bigInteger('quantity');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('size_id')->references('id')->on('sizes');
            $table->foreign('agent_id')->references('id')->on('agents');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_properties');
    }
};
