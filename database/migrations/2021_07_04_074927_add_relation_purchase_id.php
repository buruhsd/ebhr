<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationPurchaseId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_letter_items', function (Blueprint $table) {
            $table->UnsignedBigInteger('purchase_letter_id')->nullable();
            $table->string('unit')->nullable();
            $table->integer('qty')->nullable();

            $table->foreign('purchase_letter_id')->references('id')->on('purchase_letters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_letter_items', function (Blueprint $table) {
            $table->dropColumn('purchase_letter_id');
            $table->dropColumn('unit');
            $table->dropColumn('qty');
        });
    }
}
