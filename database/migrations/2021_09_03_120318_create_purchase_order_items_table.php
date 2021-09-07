<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_letter_item_id');
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('unit_id');
            $table->decimal('qty',8,2);
            $table->double('price',20,2);
            $table->integer('discount');
            $table->double('net',20,2);
            $table->integer('status')->default(0);
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('purchase_letter_item_id')->references('id')->on('purchase_letter_items');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('insertedBy')->references('id')->on('users');
            $table->foreign('updatedBy')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
    }
}
