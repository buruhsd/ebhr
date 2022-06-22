<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('customer_group_id');
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->smallInteger('term_of_payment');
            $table->boolean('is_tt')->default(false);

            $table->foreign('partner_id')->references('id')->on('partners');
            $table->foreign('customer_group_id')->references('id')->on('customer_groups');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
