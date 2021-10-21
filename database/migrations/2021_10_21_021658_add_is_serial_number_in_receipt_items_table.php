<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSerialNumberInReceiptItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receipt_items', function (Blueprint $table) {
            $table->boolean('is_serial_number')->default(false)->after('net_idr');
            $table->boolean('is_return')->default(false)->after('is_serial_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receipt_items', function (Blueprint $table) {
            //
        });
    }
}
