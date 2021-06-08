<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkPattern;
use App\Models\WorkGroup;
use App\Models\WorkType;
use App\Models\EmployeeStatus;
use App\Models\Position;
use App\Models\IdentityCard;

use \Faker\Factory;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $wp = WorkPattern::create([
        // 	'code' => '6-1-N-N',
        // 	'name' => '6 Hari Kerja, 1 Hari Libur, Non Shift, Non Group  ',
        // 	'presensi' => '2',
        // 	'updatedBy' => 1,
        // 	'insertedBy' => 1,
        // ]);

        // $wg = WorkGroup::create([
        // 	'code' => 'A',
        // 	'name' => 'Group A',
        // 	'updatedBy' => 1,
        // 	'insertedBy' => 1,
        // ]);

        // $wt = WorkType::create([
        // 	'code' => 'SWASTA',
        // 	'type_name' => 'Karyawan Swasta',
        // 	'updatedBy' => 1,
        // 	'insertedBy' => 1,
        // ]);

        // $pst = Position::create([
        // 	'code_position' => 'J22',
        // 	'name' => 'Pengemudi On Road',
        // 	'code_shorting' => '09',
        // 	'is_struktural' => 'ya',
        // 	'updatedBy' => 1,
        // 	'insertedBy' => 1,
        // ]);

        // for ($i=0; $i < 100000; $i++) {
        // 	$faker = \Faker\Factory::create('id_ID');
        // 	// $fakernik = \Faker\Provider\id_ID\Person;
        // 	$idcard = IdentityCard::create([
        // 		'nik' => $faker->nik(),
        // 		'name' => $faker->name(),
        // 		'gender' => 'laki-laki',
        // 		'date_of_birth' => $faker->dateTime(),
        // 		'blood_type' => 'A',
        // 		'religion_id' => 1,
        // 		'work_type_id' => 1,
        // 		'nationality' => 'WNI',
        // 		'marital_status_id' => 1,
        // 		'address' => $faker->address(),
        // 		'rt' => '1',
        // 		'rw' => '1',
        // 		'postal_code_id' => 1,
        // 		'village_id' => 1101012001,
        // 		'district_id' => 110101,
        // 		'regency_id' => 1101,
        // 		'province_id' => 11,
        // 		'published_date_ktp' => $faker->dateTime(),
        // 		'updatedBy' => 1,
        // 		'insertedBy' => 1,
        // 	]);
        // }


    }
}
