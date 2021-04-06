<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevelopmentStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('development_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->UnsignedBigInteger('insertedBy')->nullable();
            $table->UnsignedBigInteger('updatedBy')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('development_statuses');
    }
}
