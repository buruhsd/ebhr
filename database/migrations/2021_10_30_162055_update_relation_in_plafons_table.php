<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRelationInPlafonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plafons', function (Blueprint $table) {
            $table->dropForeign('plafons_approval_level_id_foreign');
            $table->dropForeign('plafons_release_level_id_foreign');
            $table->foreign('approval_level_id')->references('id')->on('organization_levels');
            $table->foreign('release_level_id')->references('id')->on('organization_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plafons', function (Blueprint $table) {
            //
        });
    }
}
