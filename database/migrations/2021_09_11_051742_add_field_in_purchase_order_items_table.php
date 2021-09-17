<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInPurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->double('price_hc',20,2)->after('price');
            $table->double('net_hc',20,2)->after('net');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            //
        });
    }
}
