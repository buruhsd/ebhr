<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_surat');
            $table->string('no_surat');
            $table->string('no_induk');
            $table->string('name_alias');
            $table->UnsignedBigInteger('identity_id');
            $table->UnsignedBigInteger('work_pattern_id');
            $table->UnsignedBigInteger('work_group_id');
            $table->UnsignedBigInteger('position_id');
            $table->UnsignedBigInteger('employee_status_id');
            $table->UnsignedBigInteger('development_status_id');
            $table->UnsignedBigInteger('position_id');
            $table->date('start_date');
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
        Schema::dropIfExists('employees');
    }
}
