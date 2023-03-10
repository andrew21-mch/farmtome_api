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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // orders is made by a user, to a farm or shop
            $table->bigInteger('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('users');
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products');
            $table->bigInteger('AgroInput_id')->unsigned()->nullable();
            $table->foreign('AgroInput_id')->references('id')->on('agro_inputs');
            $table->bigInteger('farm_id')->unsigned()->nullable();
            $table->foreign('farm_id')->references('id')->on('farms');
            $table->bigInteger('supplier_shop_id')->unsigned()->nullable();
            $table->foreign('supplier_shop_id')->references('id')->on('supplier_shops');
            $table->string('status')->default('pending');
            $table->string('payment_method')->default('cash');
            $table->string('payment_status')->default('pending');
            $table->string('delivery_method')->default('pickup');
            $table->bigInteger('quantity')->default(1);
            $table->string('delivery_address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
