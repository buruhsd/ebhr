<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('purchase_order_id');
            $table->string('number',20)->unique();
            $table->date('date');
            $table->integer('ppn');
            $table->integer('term_of_payment');
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->unsignedBigInteger('kurs_type_id')->nullable();
            $table->decimal('kurs',8,2)->nullable();
            $table->double('ppn_valas',20,4)->default(0);
            $table->double('ppn_idr',20,2)->default(0);
            $table->double('total_valas',20,4)->default(0);
            $table->double('total_idr',20,2)->default(0);
            $table->double('dpp',20,2)->default(0);
            $table->text('noted');
            $table->integer('status')->default(0);
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('kurs_type_id')->references('id')->on('kurs_types');
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
        Schema::dropIfExists('receipts');
    }
}
