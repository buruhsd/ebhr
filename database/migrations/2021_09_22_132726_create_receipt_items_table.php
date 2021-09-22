<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipt_id');
            $table->unsignedBigInteger('purchase_order_item_id');
            $table->unsignedBigInteger('product_status_id');
            $table->unsignedBigInteger('unit_op_id');
            $table->unsignedBigInteger('unit_id');
            $table->decimal('qty_op',8,2);
            $table->decimal('qty',8,2);
            $table->double('price_valas',20,4);
            $table->double('net_valas',20,4);
            $table->double('kurs',20,2);
            $table->integer('discount');
            $table->double('price_idr',20,2);
            $table->double('net_idr',20,2);
            $table->integer('status')->default(0);
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('receipt_id')->references('id')->on('receipts');
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items');
            $table->foreign('product_status_id')->references('id')->on('product_statuses');
            $table->foreign('unit_op_id')->references('id')->on('units');
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
        Schema::dropIfExists('receipt_items');
    }
}
