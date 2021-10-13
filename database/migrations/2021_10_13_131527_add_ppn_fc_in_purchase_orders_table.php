<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPpnFcInPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->double('ppn_fc',20,4)->default(0)->after('term_of_payment');
            $table->double('total_fc',20,4)->default(0)->after('ppn_hc');
            $table->double('total_hc',20,2)->default(0)->after('total_fc');
            $table->double('grand_total_fc',20,4)->default(0)->after('dpp');
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
