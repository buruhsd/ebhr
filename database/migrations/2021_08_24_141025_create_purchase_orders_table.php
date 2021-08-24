<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('purchase_letter_id');
            $table->unsignedBigInteger('purchase_letter_item_id');
            $table->unsignedBigInteger('transaction_type_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('no_op',20)->unique();
            $table->date('date_op');
            $table->date('date_estimate');
            $table->integer('ppn');
            $table->integer('qty');
            $table->double('price',20,2);
            $table->integer('discount');
            $table->double('net',20,2);
            $table->integer('status')->default(0);
            $table->text('noted');
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('purchase_letter_id')->references('id')->on('purchase_letters');
            $table->foreign('purchase_letter_item_id')->references('id')->on('purchase_letter_items');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
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
        Schema::dropIfExists('purchase_orders');
    }
}
