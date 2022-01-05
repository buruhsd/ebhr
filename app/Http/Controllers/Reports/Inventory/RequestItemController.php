<?php

namespace App\Http\Controllers\Reports\Inventory;

use Auth;
use App\Models\CompanyInfo;
use App\Models\Inventory\RequestItem;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function exportPdf(Request $request,$id)
    {
        $data = RequestItem::find($id);
        if(is_null($data)){
            return response()->json(['succces'=>false,'message'=>'Data tidak ada']);
        }
        $company = CompanyInfo::first();
        $client = new Party([
            'user'          => Auth::user()->name,
            'name'          => $company->name,
            'custom_fields' => [
                'Cabang'   => $data->branch->name,
                'Peminta'   => $data->organization->name,
                'Jenis SPB'   => $data->bpb_type->name,
                'Kel Penggunaan'   => $data->usage_group->name,
            ],
        ]);

        $customer = new Party([
            'custom_fields' => [
                'Tanggal SPB'   => date('d-m-Y', strtotime($data->date_spb)),
                'No PKB'   => $data->number_pkb,
                'Tanggal PKB'   => date('d-m-Y', strtotime($data->date_pkb)),
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
            array_push($items,$item);
        }
        $filename = 'surat_permintaan_barang_'.$data->number.'_'.time();
        $invoice = Invoice::make('Surat Permintaan Barang')
            ->status(__('invoices::invoice.paid'))
            ->serialNumberFormat($data->number_spb)
            ->seller($client)
            ->buyer($customer)
            ->date(now()->subWeeks(3))
            ->dateFormat('m/d/Y')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($filename)
            ->addItems($items)
            ->template('spb')
            ->logo(url('images/ebs.png'))
            ->save('public');

        // return $invoice->download();
        return $invoice->stream();
    }
}
