<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSerialNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->UnsignedBigInteger('product_id');
            $table->boolean('is_return')->default(false);
            $table->boolean('is_serial_number')->default(false);
            $table->boolean('status')->default(true);
            $table->UnsignedBigInteger('insertedBy')->nullable();
            $table->UnsignedBigInteger('updatedBy')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
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
        Schema::dropIfExists('product_serial_numbers');
    }
}
