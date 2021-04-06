<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdentityCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('identity_cards', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->string('name');
            $table->enum('gender', ['laki-laki', 'perempuan']);
            $table->date('date_of_birth');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O']);
            $table->unsignedBigInteger('religion_id');
            $table->unsignedBigInteger('work_type_id');
            $table->enum('nationality', ['WNI', 'WNA']);
            $table->unsignedBigInteger('marital_status_id');
            $table->text('address');
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->unsignedBigInteger('postal_code_id');
            $table->unsignedBigInteger('village_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('regency_id');
            $table->unsignedBigInteger('province_id');
            $table->date('published_date_ktp');
            $table->text('description')->nullable();
            $table->UnsignedBigInteger('insertedBy')->nullable();
            $table->UnsignedBigInteger('updatedBy')->nullable();
            $table->timestamps();

            $table->foreign('religion_id')->references('id')->on('religions')->onDelete('cascade');
            $table->foreign('work_type_id')->references('id')->on('work_types')->onDelete('cascade');
            $table->foreign('marital_status_id')->references('id')->on('marital_statuses')->onDelete('cascade');
            $table->foreign('postal_code_id')->references('id')->on('postal_codes')->onDelete('cascade');
            $table->foreign('village_id')->references('id')->on('villages')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->foreign('regency_id')->references('id')->on('regencies')->onDelete('cascade');
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            
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
        Schema::dropIfExists('identity_cards');
    }
}
