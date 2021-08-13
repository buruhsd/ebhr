<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateValueBloodTypeInIdentityCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('identity_cards', function (Blueprint $table) {
            DB::statement("ALTER TABLE identity_cards MODIFY COLUMN blood_type ENUM('A', 'B', 'AB', 'O', '-')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('identity_cards', function (Blueprint $table) {
            //
        });
    }
}
