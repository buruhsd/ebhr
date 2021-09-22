<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClosedInPurchaseLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_letters', function (Blueprint $table) {
            $table->UnsignedBigInteger('closed_by')->nullable()->after('purchase_urgensity_id');
            $table->timestamp('closed_at')->nullable()->after('closed_by');
            $table->foreign('closed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_letters', function (Blueprint $table) {
            //
        });
    }
}
