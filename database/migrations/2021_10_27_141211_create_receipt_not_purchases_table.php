<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptNotPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_not_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('pbp_type_id');
            $table->unsignedBigInteger('product_expenditure_id')->nullable();
            $table->unsignedBigInteger('original_warehouse_id')->nullable();
            $table->string('number')->unique();
            $table->date('date');
            $table->string('note');
            $table->boolean('status')->default(false);
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('pbp_type_id')->references('id')->on('pbp_types');
            $table->foreign('product_expenditure_id')->references('id')->on('product_expenditures');
            $table->foreign('original_warehouse_id')->references('id')->on('warehouses');
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
        Schema::dropIfExists('receipt_not_purchases');
    }
}
