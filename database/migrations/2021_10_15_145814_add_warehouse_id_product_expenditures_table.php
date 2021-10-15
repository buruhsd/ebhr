<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdProductExpendituresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_expenditures', function (Blueprint $table) {
            $table->dropForeign('product_expenditures_destination_branch_id_foreign');
            $table->dropColumn('destination_branch_id');
            $table->unsignedBigInteger('warehouse_id')->after('bpb_type_id');
            $table->unsignedBigInteger('destination_warehouse_id')->after('warehouse_id')->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('destination_warehouse_id')->references('id')->on('warehouses');
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
