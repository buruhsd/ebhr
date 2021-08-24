<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_descriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->string('item_name');
            $table->string('brand');
            $table->string('type');
            $table->text('noted');
            $table->integer('status')->default(0);
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreign('insertedBy')->references('id')->on('users');
            $table->foreign('updatedBy')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_descriptions');
    }
}
