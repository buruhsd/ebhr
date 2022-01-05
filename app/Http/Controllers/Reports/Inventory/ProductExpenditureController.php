<?php

namespace App\Http\Controllers\Reports\Inventory;

use Auth;
use App\Models\CompanyInfo;
use App\Models\Inventory\ProductExpenditure;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductExpenditureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function exportPdf(Request $request,$id)
    {
        $data = ProductExpenditure::find($id);
        if(is_null($data)){
            return response()->json(['succces'=>false,'message'=>'Data tidak ada']);
        }
        $company = CompanyInfo::first();
        $client = new Party([
            'user'          => Auth::user()->name,
            'name'          => $company->name,
            'custom_fields' => [
                'Cabang'   => $data->branch->name,
                'Gudang'   => $data->warehouse->name,
                'Tujuan Gudang'   => $data->destination_warehouse ? $data->destination_warehouse->name : '-',
                'Jenis SPB'   => $data->bpb_type->name,
            ],
        ]);

        $customer = new Party([
            'custom_fields' => [
                'Tanggal BPB'   => date('d-m-Y', strtotime($data->date_bpb)),
                'No SPB'   => $data->request_item->number_spb,
                'Tanggal SPB'   => date('d-m-Y', strtotime($data->request_item->date_spb)),
                'Keterangan'   => $data->note
            ],
        ]);

        $items = [];
        foreach($data->detail_items()->get() as $value){
            $item = (new InvoiceItem())
                ->title($value->product->name)
                ->pricePerUnit(0)
                ->quantity($value->qty)
                ->units($value->unit->name);
            $item->register_number = $value->product->register_number;
            $item->status = $value->product_status->name;
            array_push($items,$item);
        }
        $filename = 'bukti_pengeluaran_barang_'.$data->number_bpb.'_'.time();
        $invoice = Invoice::make('Bukti Pengeluaran Barang')
            ->status(__('invoices::invoice.paid'))
            ->serialNumberFormat($data->number_bpb)
            ->seller($client)
            ->buyer($customer)
            ->date(now()->subWeeks(3))
            ->dateFormat('m/d/Y')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($filename)
            ->addItems($items)
            ->template('expenditure')
            ->logo(url('images/ebs.png'))
            ->save('public');

        // return $invoice->download();
        return $invoice->stream();
    }
}
