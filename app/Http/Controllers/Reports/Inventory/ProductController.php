<?php

namespace App\Http\Controllers\Reports\Inventory;

use DB;
use Auth;
use App\Models\Master\Products;
use App\Models\CompanyInfo;
use App\Models\Master\Warehouse;
use App\Models\Inventory\Receipt;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
    	$date = $request->date;
    	$branch = $request->branch;
    	$warehouse = $request->warehouse;
        $data = Products::select('id','register_number','second_name', DB::raw('FLOOR(0+1) as stock'))
            ->with(
                'minmax:id,product_id,warehouse_id,min,max,expired_at',
            )
            ->withCount([
                'items_pp AS qty_pp' => function ($query) {
                    $query->select(DB::raw("CAST(COALESCE(SUM(qty),0) as UNSIGNED) as qty"));
                },
                'items_op AS qty_op' => function ($query) {
                    $query->select(DB::raw("CAST(COALESCE(SUM(qty),0) as UNSIGNED) as qty"));
                },
                'items_spb AS qty_spb' => function ($query) {
                    $query->select(DB::raw("CAST(COALESCE(SUM(qty),0) as UNSIGNED) as qty"));
                }
            ])
            ->when($warehouse, function ($query) use ($warehouse) {
                $query->whereHas('minmax', function ($q) use ($warehouse) {
                    $q->where('limit_stocks.warehouse_id', $warehouse);
                });
            })
            ->when($branch, function ($query) use ($branch) {
                $query->whereHas('minmax', function ($q) use ($branch) {
                    $id = Warehouse::where('branch_id',$branch)->pluck('id');
                    $q->whereIn('limit_stocks.warehouse_id', $id);
                });
            })
            ->paginate(20);
        return response()->json($data);
    }

    public function detail (Request $request,$id)
    {
        $data = Products::select('id','register_number','second_name')
            ->where('id',$id)
            ->with(
                'items_pp:id,purchase_letter_id,product_id,qty,unit',
                'items_pp.purchase:id,no_pp,tgl_pp',
                'items_pp.products:id,register_number,second_name',
                'items_op:id,purchase_order_id,qty,unit_id,product_id',
                'items_op.product:id,register_number,second_name',
                'items_op.unit:id,name',
                'items_op.purchase_order:id,supplier_id,no_op,date_op',
                'items_op.purchase_order.supplier:id,partner_id',
                'items_op.purchase_order.supplier.partner:id,name',
                'items_spb:id,request_item_id,product_id,unit_id,qty',
                'items_spb.product:id,register_number,second_name',
                'items_spb.unit:id,name',
                'items_spb.request_item:id,number_spb,date_spb',
            )
            ->first();
        return response()->json($data);
    }
}
