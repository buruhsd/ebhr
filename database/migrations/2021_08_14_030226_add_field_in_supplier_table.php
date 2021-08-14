<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('name');
            $table->unsignedBigInteger('partner_id')->after('id');
            $table->unsignedBigInteger('supplier_category_id')->after('partner_id');
            $table->unsignedBigInteger('currency_id')->after('supplier_category_id');
            $table->smallInteger('term_of_payment')->after('currency_id');
            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('supplier_category_id')->references('id')->on('supplier_categories');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier', function (Blueprint $table) {
            //
        });
    }
}
