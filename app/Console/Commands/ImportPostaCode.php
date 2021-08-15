<?php

namespace App\Console\Commands;

use App\Models\PostalCode;
use App\Imports\ImportPost;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportPostaCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ImportPostaCode';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = public_path('kodepos.json');
        $json = json_decode(file_get_contents($path), true);
        foreach($json as $key => $row){
            PostalCode::create([
                'postal_code' => $row['postal_code'],
                'village_name' => $row['urban'],
                'district_name' => $row['district'],
                'regency_name' => $row['city'],
                'province_name' => $row['province']
            ]);
            echo $key."\n";
        }

    }
}
