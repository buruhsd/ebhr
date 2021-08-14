<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code',25)->unique();
            $table->string('name');
            $table->string('agency');
            $table->text('address');
            $table->string('block',6)->nullable();
            $table->smallInteger('no');
            $table->smallInteger('rt');
            $table->smallInteger('rw');
            $table->boolean('is_pkp')->default(true);
            $table->boolean('is_closed')->default(true);
            $table->unsignedBigInteger('npwp_id');
            $table->unsignedBigInteger('postal_code_id');
            $table->unsignedBigInteger('village_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('regency_id');
            $table->unsignedBigInteger('province_id');
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('npwp_id')->references('id')->on('npwps');
            $table->foreign('postal_code_id')->references('id')->on('postal_codes');
            $table->foreign('village_id')->references('id')->on('villages');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->foreign('regency_id')->references('id')->on('regencies');
            $table->foreign('province_id')->references('id')->on('provinces');

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
        Schema::dropIfExists('partners');
    }
}
