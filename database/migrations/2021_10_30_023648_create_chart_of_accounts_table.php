<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->bigInteger('code')->unique();
            $table->string('name');
            $table->integer('level');
            $table->string('normal_balance',10);
            $table->string('detail_general',10);
            $table->string('classification');
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->boolean('is_close')->default(false);
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('chart_of_accounts');
            $table->foreign('currency_id')->references('id')->on('currencies');
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
        Schema::dropIfExists('chart_of_accounts');
    }
}
