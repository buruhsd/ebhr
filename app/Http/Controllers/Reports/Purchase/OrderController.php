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

    public function order_not_ttb(Request $request)
    {
    	$from_date = $request->from_date;
    	$to_date = $request->to_date;
    	$branch = $request->branch;
    	$search = $request->search;
    	$status = $request->status;
        $sortBy = $request->input('sortby');
        $orderBy = $request->input('orderby');
        if(is_null($orderBy)){
            $orderBy = 'id';
        }
        if(is_null($sortBy)){
            $sortBy = 'desc';
        }

        if(is_null($from_date) || is_null($to_date)){
            $to_date = date('Y-m-d');
            $from_date = date('Y-m-d', strtotime($to_date. '-10 months'));
        }

    	$data = PurchaseOrder::with('branch:id,name',
                    'transaction_type:id,name',
                    'supplier:id,partner_id,supplier_category_id,currency_id,term_of_payment',
                    'supplier.currency:id,code,name',
                    'supplier.partner:id,code,name',
                    'kurs_type:id,name',
                    'currency:id,code,name',
                    'approved_by:id,name',
                    'released_by:id,name',
                    'closed_by:id,name',
                    'order_item',
                    'order_item.purchase:id,no_pp',
                    'order_item.purchase_item:id,product_id,qty,rest_qty,unit',
                    'order_item.product:id,register_number,name,second_name',
                    'order_item.product.units:id,name,value',
                    'order_item.unit:id,name',
                    'insertedBy:id,name',
                    'updatedBy:id,name')
                ->when($status, function ($query) use ($status){
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
                ->when($search, function ($query) use ($search){
                    $query->where('no_op','LIKE',"{$search}%");
                })
                ->when($branch, function ($query) use ($branch){
                    $query->whereHas('branch',function ($q) use ($branch){
                        $q->where('branches.id',$branch);
                    });
                })
                ->whereDate('date_op','>=',$from_date)
                ->whereDate('date_op','<=',$to_date)
                ->orderBy($orderBy, $sortBy)
                ->paginate(20);
        return response()->json($data);
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
            ->logo(url('images/ebs.png'))
            ->save('public');

        // return $invoice->download();
        return $invoice->stream();
    }
}
