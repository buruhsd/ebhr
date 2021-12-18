<?php

namespace App\Exports\Purchase\Sheets;

use App\Models\Purchase\PurchaseOrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;

class DetailOderSheet implements FromQuery, WithHeadings,
ShouldAutoSize, WithMapping, WithColumnFormatting, WithTitle
{
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function title(): string
    {
        return 'Detail Order Pembelian';
    }

    public function headings(): array
    {
        return [
            "Tanggal OP",
            "Nomor OP",
            "Nomor Register",
            "Nama Barang",
            "Qty PP",
            "Satuan PP",
            "Qty OP",
            "Satuan OP",
            "Harga Satuan (FC)",
            "Harga Satuan (HC)",
            "Diskon (%)",
            "Harga Net (FC)",
            "Harga Net (HC)",
            "Jumlah (FC)",
            "Jumlah (HC)",
        ];
    }

    public function map($order): array
    {
        return [
            $order->purchase_order->date_op,
            $order->purchase_order->no_op,
            $order->product->register_number,
            $order->product->name,
            $order->purchase_item->qty,
            $order->purchase_item->unit,
            $order->qty,
            $order->unit->name,
            $order->price,
            $order->price_hc,
            $order->discount,
            $order->net,
            $order->net_hc,
            $order->net * $order->qty,
            $order->net_hc * $order->qty,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function query()
    {
        return PurchaseOrderItem::query()
                ->whereIn('purchase_order_id',$this->params);
    }
}
