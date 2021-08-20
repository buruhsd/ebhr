<?php

namespace App\Exports;

use App\Models\Position;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PositionExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Position::select('code_position','name','code_shorting','is_struktural')->get()->each->setAppends([]);
    }

    public function headings(): array
    {
        return ["Kode", "Nama", "Kode Shorting", "Struktural"];
    }
}
