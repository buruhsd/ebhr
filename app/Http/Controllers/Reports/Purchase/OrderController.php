<?php

namespace App\Http\Controllers\Reports\Purchase;

use DB;
use Auth;
use App\Models\CompanyInfo;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseOrderItem;
use App\Models\Inventory\ReceiptItems;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use App\Exports\Purchase\OderExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
    	$from_date = $request->from_date;
    	$to_date = $request->to_date;
    	$branch = $request->branch;
    	$status = $request->status;

        if(is_null($from_date) || is_null($to_date)){
            $to_date = $to_date ? $to_date : date('Y-m-d');
            $from_date = $from_date ? $from_date :date('Y-m-d', strtotime($to_date. '-10 months'));
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
                ->whereDate('date_op','<=',$to_date)
                ->paginate(20);
        return response()->json($data);
    }

    public function export_excel(Request $request)
	{
    	$branch = $request->branch;
    	$status = $request->status;
    	$from_date = $request->from_date;
    	$to_date = $request->to_date;
        if(is_null($from_date) || is_null($to_date)){
            $request->merge([
                'to_date' => $to_date ? $to_date : date('Y-m-d'),
                'from_date' => $from_date ? $from_date : date('Y-m-d', strtotime($to_date. '-10 months')),
            ]);
        }
		return Excel::download(new OderExport($request->all()), 'purchace_order_'.time().'.xlsx');
	}

    public function exportPdf(Request $request,$order_id)
    {
        $order = PurchaseOrder::find($order_id);
        if(is_null($order)){
            return response()->json(['succces'=>false,'message'=>'Data tidak ada']);
        }
        $company = CompanyInfo::first();
        $supplier = $order->supplier->partner;
        $client = new Party([
            'user'          => Auth::user()->name,
            'name'          => $company->name,
            'custom_fields' => [
                'Tanggal'   => date('d-m-Y', strtotime($order->date_op)),
                'Kepada'   => $supplier->agency.' '.$supplier->name,
                'Alamat'   => $supplier->address,
            ],
        ]);

        $customer = new Party([
            'custom_fields' => [
                'TOP'   => $order->term_of_payment.' Hari',
                'Estimasi Kirim'   => date('d-m-Y', strtotime($order->date_estimate)),
                'Keterangan'   => $order->noted
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

    public function detail(Request $request)
    {
        $from_date = $request->from_date;
    	$to_date = $request->to_date;
    	$branch = $request->branch;
    	$status = $request->status;

        if(is_null($from_date) || is_null($to_date)){
            $to_date = $to_date ? $to_date : date('Y-m-d');
            $from_date = $from_date ? $from_date :date('Y-m-d', strtotime($to_date. '-10 months'));
        }
        $data = PurchaseOrderItem::select('id','purchase_order_id','product_id','unit_id','qty')
            ->with(
                'purchase_order:id,supplier_id,no_op,date_op,status',
                'purchase_order.supplier:id,partner_id',
                'purchase_order.supplier.partner:id,code,name',
                'product:id,register_number,name,second_name',
                'unit:id,name'
            )
            ->withCount([
                'recepit_detail AS qty_ttb' => function ($query) {
                    $query->select(DB::raw("SUM(qty/unit_conversion) as qty"));
                }
            ])
            ->whereHas('purchase_order', function ($query) use ($status,$branch,$from_date,$to_date){
                $query->when($status, function ($query) use ($status){
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
            })->paginate(10);
        return response()->json($data);
    }

    public function detail_receipt(Request $request,$id)
    {
        $data = ReceiptItems::select('id','receipt_id','unit_op_id','unit_id','unit_conversion','qty_op','qty', DB::raw('ROUND(qty / unit_conversion,2) as qty_conversion'))
            ->with(
                'receipt:id,warehouse_id,number,date',
                'receipt.warehouse:id,name',
                'unit_op:id,name',
                'unit_ttb:id,name'
            )->where('purchase_order_item_id',$id)->paginate(10);
        return response()->json($data);
    }
}
