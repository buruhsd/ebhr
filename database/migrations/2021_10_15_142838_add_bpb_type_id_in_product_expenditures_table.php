<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBpbTypeIdInProductExpendituresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_expenditures', function (Blueprint $table) {
            $table->unsignedBigInteger('bpb_type_id')->after('request_item_id');
            $table->foreign('bpb_type_id')->references('id')->on('bpb_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_expenditures', function (Blueprint $table) {
            //
        });
    }
}
