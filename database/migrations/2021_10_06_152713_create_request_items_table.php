<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('bpb_type_id');
            $table->unsignedBigInteger('usage_group_id');
            $table->string('number_spb')->unique();
            $table->date('date_spb');
            $table->string('number_pkb')->unique();
            $table->date('date_pkb');
            $table->string('note');
            $table->boolean('status')->default(false);
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('bpb_type_id')->references('id')->on('bpb_types');
            $table->foreign('usage_group_id')->references('id')->on('usage_groups');
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
        Schema::dropIfExists('request_items');
    }
}
