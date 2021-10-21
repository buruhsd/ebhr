<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationsInSerialNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('serial_numbers', function (Blueprint $table) {
            $table->unsignedBigInteger('product_expenditure_id')->nullable()->after('receipt_item_id');
            $table->unsignedBigInteger('product_expenditure_detail_id')->nullable()->after('product_expenditure_id');
            $table->foreign('product_expenditure_id')->references('id')->on('product_expenditures');
            $table->foreign('product_expenditure_detail_id')->references('id')->on('product_expenditure_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('serial_numbers', function (Blueprint $table) {
            //
        });
    }
}
