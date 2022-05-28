<?php

namespace App\Http\Controllers\Reports\Inventory;

use Auth;
use App\Models\CompanyInfo;
use App\Models\Inventory\Receipt;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:report-receipt');
    }

    public function exportPdf(Request $request,$id)
    {
        $data = Receipt::find($id);
        if(is_null($data)){
            return response()->json(['succces'=>false,'message'=>'Data tidak ada']);
        }
        $company = CompanyInfo::first();
        $supplier = $data->supplier->partner;
        $client = new Party([
            'user'          => Auth::user()->name,
            'name'          => $company->name,
            'custom_fields' => [
                'Tanggal'   => date('d-m-Y', strtotime($data->date_op)),
                'Supplier'   => $supplier->agency.' '.$supplier->name,
                'Alamat'   => $supplier->address,
                'Keterangan'   => $data->noted
            ],
        ]);

        $customer = new Party([
            'custom_fields' => [
                'No OPB'   => $data->purchase_order->no_op,
                'TOP'   => $data->term_of_payment.' Hari',
                'PPN %'   => $data->ppn
            ],
        ]);

        $items = [];
        foreach($data->receipt_items()->get() as $value){
            $item = (new InvoiceItem())
                ->title($value->product->name)
                ->pricePerUnit($value->price_idr)
                ->quantity($value->qty)
                ->units($value->unit_ttb->name);
            $item->register_number = $value->product->register_number;
            $item->status = $value->product_status->name;
            array_push($items,$item);
        }
        $filename = 'tanda_terima_barang_'.$data->number.'_'.time();
        $invoice = Invoice::make('Tanda Terima Barang')
            ->status(__('invoices::invoice.paid'))
            ->serialNumberFormat($data->number)
            ->seller($client)
            ->buyer($customer)
            ->date(now()->subWeeks(3))
            ->dateFormat('m/d/Y')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->setCustomData($data->ppn)
            ->filename($filename)
            ->addItems($items)
            ->totalTaxes($data->ppn_idr)
            ->totalAmount($data->total_idr)
            ->template('receipt')
            ->logo(url('images/ebs.png'))
            ->save('public');

        // return $invoice->download();
        return $invoice->stream();
    }

    public function warehouseExportPdf(Request $request,$id)
    {
        $data = Receipt::find($id);
        if(is_null($data)){
            return response()->json(['succces'=>false,'message'=>'Data tidak ada']);
        }
        $company = CompanyInfo::first();
        $supplier = $data->supplier->partner;
        $client = new Party([
            'user'          => Auth::user()->name,
            'name'          => $company->name,
            'custom_fields' => [
                'Tanggal'   => date('d-m-Y', strtotime($data->date_op)),
                'Supplier'   => $supplier->agency.' '.$supplier->name,
                'Alamat'   => $supplier->address,
            ],
        ]);

        $customer = new Party([
            'custom_fields' => [
                'No OPB'   => $data->purchase_order->no_op,
                'TOP'   => $data->term_of_payment.' Hari',
                'Keterangan'   => $data->noted
            ],
        ]);

        $items = [];
        foreach($data->receipt_items()->get() as $value){
            $item = (new InvoiceItem())
                ->title($value->product->name)
                ->pricePerUnit($value->price_idr)
                ->quantity($value->qty)
                ->units($value->unit_ttb->name);
            $item->register_number = $value->product->register_number;
            $item->status = $value->product_status->name;
            array_push($items,$item);
        }
        $filename = 'tanda_terima_barang_'.$data->number.'_'.time();
        $invoice = Invoice::make('Tanda Terima Barang')
            ->status(__('invoices::invoice.paid'))
            ->serialNumberFormat($data->number)
            ->seller($client)
            ->buyer($customer)
            ->date(now()->subWeeks(3))
            ->dateFormat('m/d/Y')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($filename)
            ->addItems($items)
            ->template('receipt_warehouse')
            ->logo(url('images/ebs.png'))
            ->save('public');

        // return $invoice->download();
        return $invoice->stream();
    }
}
