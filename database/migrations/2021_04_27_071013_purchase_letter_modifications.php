<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PurchaseLetterModifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_letters', function (Blueprint $table) {
            $table->UnsignedBigInteger('branch_id')->nullable();
            $table->UnsignedBigInteger('transaction_type_id')->nullable();
            $table->UnsignedBigInteger('purchase_category_id')->nullable();
            $table->UnsignedBigInteger('purchase_necessary_id')->nullable();
            $table->UnsignedBigInteger('purchase_urgensity_id')->nullable();

            $table->UnsignedBigInteger('insertedBy')->nullable();
            $table->UnsignedBigInteger('updatedBy')->nullable();

            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')->onDelete('cascade');
            $table->foreign('purchase_category_id')->references('id')->on('purchase_categories')->onDelete('cascade');
            $table->foreign('purchase_necessary_id')->references('id')->on('purchase_necessaries')->onDelete('cascade');
            $table->foreign('purchase_urgensity_id')->references('id')->on('purchase_urgentities')->onDelete('cascade');

            $table->foreign('insertedBy')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updatedBy')->references('id')->on('users')->onDelete('cascade');
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
          
            $table->dropColumn('branch_id', 'transaction_type_id', 'purchase_category_id', 'purchase_necessary_id', 'purchase_urgensity_id', 'insertedBy', 'updatedBy');
        });
    }
}
