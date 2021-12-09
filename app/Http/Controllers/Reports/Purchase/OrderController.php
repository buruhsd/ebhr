<?php

namespace App\Http\Controllers\Reports\Purchase;

use App\Models\CompanyInfo;
use App\Models\Purchase\PurchaseOrder;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function exportPdf(Request $request,$order_id)
    {
        $order = PurchaseOrder::find($order_id);
        if(is_null($order)){
            return response()->json(['succces'=>false,'message'=>'Data tidak ada']);
        }
        $company = CompanyInfo::first();
        $client = new Party([
            'name'          => $company->name,
            'address'       => $company->address,
            'custom_fields' => [
                'Phone/Fax'         => $company->phone_number.'/'.$company->fax,
            ],
        ]);

        $supplier = $order->supplier->partner;
        $customer = new Party([
            'custom_fields' => [
                'Tanggal'   => date('d-m-Y', strtotime($order->date_op)),
                'Kepada'   => $supplier->agency.' '.$supplier->name,
                'Alamat'   => $supplier->address,
                // 'Phone/Fax'         => '(024) 658-4888/024-658-2123',
            ],
        ]);

        $items = [];
        foreach($order->order_item()->get() as $value){
            $item = (new InvoiceItem())->title($value->product->name)
                ->pricePerUnit($value->price_hc)
                ->quantity($value->qty)
                ->discountByPercent($value->discount)
                ->units($value->unit->name);
            array_push($items,$item);
        }

        $notes = '';
        if($order->description){
            $notes = $order->description->noted;
        }
        $filename = 'order_pembelian_'.$order->no_op.'_'.time();
        $invoice = Invoice::make('Order Pembelian')
            ->status(__('invoices::invoice.paid'))
            ->serialNumberFormat($order->no_op)
            ->seller($client)
            ->buyer($customer)
            ->date(now()->subWeeks(3))
            ->dateFormat('m/d/Y')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->setCustomData($order->ppn)
            ->filename($filename)
            ->addItems($items)
            ->notes($notes)
            ->totalTaxes($order->ppn_hc)
            ->totalAmount($order->total_hc)
            ->template('order')
            ->save('public');

        // return $invoice->download();
        return $invoice->stream();
    }
}
