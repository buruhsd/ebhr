<?php

namespace App\Http\Controllers\Reports\Inventory;

use DB;
use Auth;
use App\Models\Master\Products;
use App\Models\CompanyInfo;
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
        $this->middleware('auth:api');
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
                })->whereHas('items_pp', function ($q) use ($warehouse) {
                    $q->whereHas('purchase', function ($pp) use ($warehouse) {
                        $pp->where('purchase_letters.warehouse_id', $warehouse);
                    });
                });
            })
            ->when($branch, function ($query) use ($branch) {
                $query->whereHas('items_pp', function ($q) use ($branch) {
                    $q->whereHas('purchase', function ($pp) use ($branch) {
                        $pp->where('purchase_letters.branch_id', $branch);
                    });
                })->whereHas('items_op', function ($q) use ($branch) {
                    $q->whereHas('purchase_order', function ($op) use ($branch) {
                        $op->where('purchase_orders.branch_id', $branch);
                    });
                })->whereHas('items_spb', function ($q) use ($branch) {
                    $q->whereHas('request_item', function ($spb) use ($branch) {
                        $spb->where('request_items.branch_id', $branch);
                    });
                });
            })
            ->paginate(20);
        return response()->json($data);
    }
}
