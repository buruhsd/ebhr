<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsOrderInPurchaseLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_letters', function (Blueprint $table) {
            $table->boolean('is_order')->default(0)->after('note');
            $table->integer('status')->default(0)->after('is_order');
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
