<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_status_id');
            $table->integer('date_year');
            $table->integer('qty_awal');
            $table->double('hpp_awal',20,4);
            $table->double('nil_awal',20,4);
            $table->integer('qty_debit');
            $table->double('nil_debit',20,4);
            $table->integer('qty_credit');
            $table->double('nil_credit',20,4);
            $table->integer('qty_akhir');
            $table->double('hpp_akhir',20,4);
            $table->double('nil_akhir',20,4);
            $table->integer('qty_temp');
            $table->date('post_date');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_status_id')->references('id')->on('product_statuses');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_balances');
    }
}
