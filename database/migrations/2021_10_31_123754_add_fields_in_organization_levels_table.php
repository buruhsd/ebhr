<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInOrganizationLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organization_levels', function (Blueprint $table) {
            $table->boolean('management')->default(false)->after('name');
            $table->integer('data_order')->default(0)->after('management');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_levels', function (Blueprint $table) {
            //
        });
    }
}
