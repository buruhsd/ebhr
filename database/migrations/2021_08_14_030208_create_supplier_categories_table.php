<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code',100)->unique();
            $table->string('name');
            $table->UnsignedBigInteger('parent_id')->nullable();
            $table->UnsignedBigInteger('insertedBy');
            $table->UnsignedBigInteger('updatedBy');
            $table->timestamps();

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
        Schema::dropIfExists('supplier_categories');
    }
}
