<?php

namespace App\Http\Controllers\Reports\Inventory;

use DB;
use Auth;
use App\Models\Purchase\PurchaseOrderItem;
use App\Models\Purchase\PurchaseLetterItem;
use App\Models\Inventory\RequestItemDetail;
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
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
    	$date = $request->date;
    	$branch = $request->branch;
    	$warehouse = $request->warehouse;
        $data = Products::select('id','register_number','second_name', DB::raw('FLOOR(0+1) as stock'))
            ->withCount([
                'min_max AS min' => function ($query) use ($branch,$warehouse) {
                    $query->select(DB::raw("CAST(COALESCE(SUM(min),0) as UNSIGNED) as min"))
                    ->when($warehouse, function ($query) use ($warehouse) {
                        $query->where('limit_stocks.warehouse_id', $warehouse);
                    })
                    ->when($branch, function ($query) use ($branch,$warehouse) {
                        $whereInId = Warehouse::where('branch_id',$branch)->pluck('id');
                        if($warehouse){
                            $query->where('limit_stocks.warehouse_id', $warehouse);
                        }else{
                            $query->whereIn('limit_stocks.warehouse_id', $whereInId);
                        }
                    });
                },
                'min_max AS max' => function ($query) use ($branch,$warehouse) {
                    $query->select(DB::raw("CAST(COALESCE(SUM(max),0) as UNSIGNED) as max"))
                    ->when($warehouse, function ($query) use ($warehouse) {
                        $query->where('limit_stocks.warehouse_id', $warehouse);
                    })
                    ->when($branch, function ($query) use ($branch,$warehouse) {
                        $whereInId = Warehouse::where('branch_id',$branch)->pluck('id');
                        if($warehouse){
                            $query->where('limit_stocks.warehouse_id', $warehouse);
                        }else{
                            $query->whereIn('limit_stocks.warehouse_id', $whereInId);
                        }
                    });
                },
                'items_pp AS qty_pp' => function ($query) use ($warehouse,$branch) {
                    $query->select(DB::raw("CAST(COALESCE(SUM(rest_qty),0) as UNSIGNED) as qty"))
                    ->when($warehouse, function ($query) use ($warehouse) {
                        $query->whereHas('purchase', function ($pp) use ($warehouse) {
                            $pp->where('purchase_letters.warehouse_id', $warehouse);
                        });
                    })
                    ->when($branch, function ($query) use ($branch) {
                        $query->whereHas('purchase', function ($pp) use ($branch) {
                            $pp->where('purchase_letters.branch_id', $branch);
                        });
                    });
                },
                'items_op AS qty_op' => function ($query) use ($branch) {
                    $query->select(DB::raw("CAST(COALESCE(SUM(rest_qty),0) as UNSIGNED) as qty"))
                    ->when($branch, function ($query) use ($branch) {
                        $query->whereHas('purchase_order', function ($pp) use ($branch) {
                            $pp->where('purchase_orders.branch_id', $branch);
                        });
                    });
                },
                'items_spb AS qty_spb' => function ($query) use ($branch) {
                    $query->select(DB::raw("CAST(COALESCE(SUM(rest_qty),0) as UNSIGNED) as qty"))
                    ->when($branch, function ($query) use ($branch) {
                        $query->whereHas('request_item', function ($pp) use ($branch) {
                            $pp->where('request_items.branch_id', $branch);
                        });
                    });
                }
            ])
            ->paginate(20);
        return response()->json($data);
    }

    public function detail_purchase(Request $request,$id)
    {
    	$branch = $request->branch;
    	$warehouse = $request->warehouse;
        $data = PurchaseLetterItem::select('id','purchase_letter_id','product_id','qty','rest_qty','unit')
            ->where('product_id',$id)
            ->where('rest_qty','>',0)
            ->whereHas('purchase', function ($query){
                $query->whereIn('purchase_letters.status',[0,1]);
            })
            ->with(
                'purchase:id,no_pp,tgl_pp,branch_id',
                'products:id,register_number,second_name',
            )
            ->when($branch, function ($query) use ($branch) {
                $query->whereHas('purchase', function ($pp) use ($branch) {
                    $pp->where('purchase_letters.branch_id', $branch);
                });
            })
            ->when($warehouse, function ($query) use ($warehouse) {
                $query->whereHas('purchase', function ($pp) use ($warehouse) {
                    $pp->where('purchase_letters.warehouse_id', $warehouse);
                });
            })
            ->get();
        return response()->json($data);
    }

    public function detail_order(Request $request,$id)
    {
    	$branch = $request->branch;
        $data = PurchaseOrderItem::select('id','purchase_order_id','unit_id','product_id','qty','rest_qty')
            ->where('product_id',$id)
            ->where('rest_qty','>',0)
            ->whereHas('purchase_order', function ($query){
                $query->whereIn('purchase_orders.status',[0,1,3,6]);
            })
            ->with(
                'purchase_order:id,branch_id,supplier_id,no_op,date_op',
                'purchase_order.supplier:id,partner_id',
                'purchase_order.supplier.partner:id,name',
                'product:id,register_number,second_name',
                'unit:id,name',
            )
            ->when($branch, function ($query) use ($branch) {
                $query->whereHas('purchase_order', function ($pp) use ($branch) {
                    $pp->where('purchase_orders.branch_id', $branch);
                });
            })
            ->get();
        return response()->json($data);
    }

    public function detail_request(Request $request,$id)
    {
    	$branch = $request->branch;
    	$warehouse = $request->warehouse;
        $data = RequestItemDetail::select('id','request_item_id','unit_id','product_id','qty','rest_qty')
            ->where('product_id',$id)
            ->where('rest_qty','>',0)
            ->whereHas('request_item', function ($query){
                $query->doesntHave('expenditure');
            })
            ->with(
                'request_item:id,number_spb,date_spb,branch_id',
                'product:id,register_number,second_name',
                'unit:id,name',
            )
            ->when($branch, function ($query) use ($branch) {
                $query->whereHas('request_item', function ($pp) use ($branch) {
                    $pp->where('request_items.branch_id', $branch);
                });
            })
            ->get();
        return response()->json($data);
    }
}
