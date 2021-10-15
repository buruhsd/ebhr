<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductExpenditureDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_expenditure_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_expenditure_id');
            $table->unsignedBigInteger('request_item_detail_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_status_id');
            $table->unsignedBigInteger('unit_id');
            $table->double('qty',20,2)->default(0);
            $table->boolean('is_serial_number')->default(false);
            $table->boolean('status')->default(false);
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('product_expenditure_id')->references('id')->on('product_expenditures');
            $table->foreign('request_item_detail_id')->references('id')->on('request_item_details');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_status_id')->references('id')->on('product_statuses');
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
        Schema::dropIfExists('product_expenditure_details');
    }
}
