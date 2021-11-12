<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_cards', function (Blueprint $table) {
            $table->id();
            $table->string('trx_code')->nullable();
            $table->string('trx_urut')->nullable();
            $table->date('trx_date')->nullable();
            $table->string('trx_jenis')->nullable();
            $table->string('trx_dbcr')->nullable();
            $table->string('scu_code')->nullable();
            $table->string('inv_code')->nullable();
            $table->string('loc_code')->nullable();
            $table->string('statusProduct')->nullable();
            $table->string('trx_kuan')->nullable();
            $table->string('hargaSatuan')->nullable();
            $table->string('trx_amnt')->nullable();
            $table->string('trx_totl')->nullable();
            $table->string('trx_hpok')->nullable();
            $table->string('trx_havg')->nullable();
            $table->string('pos_date')->nullable();
            $table->string('sal_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_cards');
    }
}
