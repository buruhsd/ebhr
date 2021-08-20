<?php

namespace App\Exports;

use App\Models\Organization;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrganizationExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Organization::select('code','name','level','description')->get()->each->setAppends([]);
    }

    public function headings(): array
    {
        return ["Kode", "Nama", "Level", "Keterangan"];
    }
}
