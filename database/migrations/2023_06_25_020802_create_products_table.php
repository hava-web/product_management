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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('category');
            $table->text('description');
            $table->unsignedBigInteger('brand');
            $table->decimal('original_price',12,2);
            $table->decimal('selling_price',12,2);
            $table->integer('quantity');
            $table->string('status');
            $table->date('imported_date');
            $table->date('expired_date');
            $table->unsignedBigInteger('warehouse_id');
            $table->string('delivered_from');
            $table->tinyInteger('sale_percentage');
            $table->foreign('category')->references('id')->on('categories');
            $table->foreign('brand')->references('id')->on('brands');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
