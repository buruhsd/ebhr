<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnBpbDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_bpb_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_bpb_id');
            $table->unsignedBigInteger('product_expenditure_detail_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_status_id');
            $table->unsignedBigInteger('unit_id');
            $table->double('qty',20,2)->default(0);
            $table->double('price',20,2)->default(0);
            $table->boolean('status')->default(false);
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('return_bpb_id')->references('id')->on('return_bpbs');
            $table->foreign('product_expenditure_detail_id')->references('id')->on('product_expenditure_details');
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
        Schema::dropIfExists('return_bpb_details');
    }
}
