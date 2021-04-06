<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ViewWilayahAdministratifIndonesia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement($this->dropView());
    }

    private function createView(): string
    {
        return <<<SQL

            CREATE VIEW view_wilayah_administratif_indonesia AS
                SELECT 
                    villages.id AS village_id, 
                    villages.name AS village_name, 
                    districts.id AS district_id,
                    districts.name AS district_name,
                    regencies.id AS regency_id,
                    regencies.name AS regency_name,
                    provinces.id AS province_id,
                    provinces.name AS province_name
                    
                FROM villages 
                    LEFT JOIN districts ON districts.id = villages.district_id
                    LEFT JOIN regencies ON regencies.id = districts.regency_id
                    LEFT JOIN provinces ON provinces.id = regencies.province_id
            SQL;
    }
   
  // VIEW `view_wilayah_administratif_indonesia`  AS select `villages`.`id` AS `village_id`,`villages`.`name` AS `village_name`,`districts`.`id` AS `district_id`,`districts`.`name` AS `district_name`,`regencies`.`id` AS `regency_id`,`regencies`.`name` AS `regency_name`,`provinces`.`id` AS `province_id`,`provinces`.`name` AS `province_name` from (((`villages` left join `districts` on((`districts`.`id` = `villages`.`district_id`))) left join `regencies` on((`regencies`.`id` = `districts`.`regency_id`))) left join `provinces` on((`provinces`.`id` = `regencies`.`province_id`))) ;
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    private function dropView(): string
    {
        return <<<SQL

            DROP VIEW IF EXISTS `view_wilayah_administratif_indonesia`;
            SQL;
    }
}
