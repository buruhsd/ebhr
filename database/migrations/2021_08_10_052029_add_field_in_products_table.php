<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->UnsignedBigInteger('unit_id')->nullable()->after('id');
            $table->UnsignedBigInteger('category_id')->nullable()->after('unit_id');
            $table->string('register_number')->after('id');
            $table->string('second_name')->after('name');
            $table->string('spesification')->after('name');
            $table->string('product_number')->after('spesification');
            $table->string('type')->after('spesification');
            $table->string('brand')->after('type');
            $table->string('vendor')->after('brand');
            $table->string('barcode')->after('vendor');
            $table->integer('status')->after('barcode');

            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
