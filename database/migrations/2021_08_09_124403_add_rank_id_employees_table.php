<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRankIdEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->UnsignedBigInteger('rank_id')->nullable()->after('identity_id');
            $table->UnsignedBigInteger('point_hire_id')->nullable()->after('rank_id');
            $table->foreign('rank_id')->references('id')->on('ranks')->onDelete('cascade');
            $table->foreign('point_hire_id')->references('id')->on('point_of_hires')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
