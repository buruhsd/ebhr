<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthoritySpbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authority_spbs', function (Blueprint $table) {
            $table->id();
            $table->UnsignedBigInteger('product_id');
            $table->UnsignedBigInteger('approval_level_id');
            $table->UnsignedBigInteger('insertedBy')->nullable();
            $table->UnsignedBigInteger('updatedBy')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('approval_level_id')->references('id')->on('organization_levels');
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
        Schema::dropIfExists('authority_spbs');
    }
}
