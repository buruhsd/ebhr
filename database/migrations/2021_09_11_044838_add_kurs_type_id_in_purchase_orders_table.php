<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKursTypeIdInPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('currency_id')->nullable()->after('max_price_item');
            $table->unsignedBigInteger('kurs_type_id')->nullable()->after('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('kurs_type_id')->references('id')->on('kurs_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            //
        });
    }
}
