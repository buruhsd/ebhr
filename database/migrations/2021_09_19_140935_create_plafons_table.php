<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlafonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plafons', function (Blueprint $table) {
            $table->id();
            $table->double('max_price_unit',20,2)->default(0);
            $table->double('max_amount_item',20,2)->default(0);
            $table->double('max_amount_op',20,2)->default(0);
            $table->date('used_at')->nullable();
            $table->unsignedBigInteger('approval_level_id');
            $table->unsignedBigInteger('release_level_id');
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();
            $table->foreign('approval_level_id')->references('id')->on('positions');
            $table->foreign('release_level_id')->references('id')->on('positions');
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
        Schema::dropIfExists('plafons');
    }
}
