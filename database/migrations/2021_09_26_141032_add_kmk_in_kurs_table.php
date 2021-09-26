<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKmkInKursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kurs', function (Blueprint $table) {
            $table->string('number_kmk',50)->nullable()->after('date');
            $table->date('kmk_at')->nullable()->after('number_kmk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kurs', function (Blueprint $table) {
            //
        });
    }
}
