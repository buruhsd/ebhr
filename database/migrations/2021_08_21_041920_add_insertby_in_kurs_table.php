<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInsertbyInKursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kurs', function (Blueprint $table) {
            $table->UnsignedBigInteger('insertedBy')->after('date');
            $table->UnsignedBigInteger('updatedBy')->after('insertedBy');
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
        Schema::table('kurs', function (Blueprint $table) {
            //
        });
    }
}
