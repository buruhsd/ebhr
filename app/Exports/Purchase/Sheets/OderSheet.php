<?php

namespace App\Exports\Purchase\Sheets;

use App\Models\Purchase\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;

class OderSheet implements FromQuery, WithHeadings,
    ShouldAutoSize, WithMapping, WithColumnFormatting, WithTitle
{
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function title(): string
    {
        return 'Order Pembelian';
    }

    public function headings(): array
    {
        return [
            "Tanggal OP",
            "Nomor OP",
            "Tanggal Estimasi",
            "Cabang",
            "Supplier",
            "TOP",
            "Jenis Transaksi",
            "Status",
            "PPN",
            "Nilai OP",
            "Dibuat Oleh",
            "Tanggal Buat",
        ];
    }

    public function map($order): array
    {
        return [
            $order->date_op,
            $order->no_op,
            $order->date_estimate,
            $order->branch->name,
            $order->supplier->partner->name,
            $order->term_of_payment,
            $order->transaction_type->name,
            $order->status_text,
            $order->ppn,
            $order->total,
            $order->inserted_by->name,
            $order->created_at,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function query()
    {
        $status = $this->params['status'];
        $branch = $this->params['branch'];
        $from_date = $this->params['from_date'];
        $to_date = $this->params['to_date'];
        return PurchaseOrder::query()
                ->when($status, function ($query) use ($status){
                    $statusIn = [0,1,2,3,4,6];
                    if($status == 'new'){
                        $statusIn = [0];
                    }elseif($status == 'app'){
                        $statusIn = [1];
                    }elseif($status == 'reject_app'){
                        $statusIn = [2];
                    }elseif($status == 'release'){
                        $statusIn = [3];
                    }elseif($status == 'reject_release'){
                        $statusIn = [4];
                    }elseif($status == 'close'){
                        $statusIn = [5];
                    }elseif($status == 'on_proses'){
                        $statusIn = [6];
                    }elseif($status == 'done'){
                        $statusIn = [7];
                    }elseif($status == 'all'){
                        $statusIn = [0,1,2,3,4,6];
                    }
                    $query->whereIn('status',$statusIn);
                })
                ->when($branch, function ($query) use ($branch){
                    $query->whereHas('branch',function ($q) use ($branch){
                        $q->where('branches.id',$branch);
                    });
                })
                ->whereDate('date_op','>=',$from_date)
                ->whereDate('date_op','<=',$to_date);
    }
}
