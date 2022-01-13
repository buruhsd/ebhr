<?php

namespace App\Http\Controllers\Reports\Inventory;

use DB;
use Auth;
use App\Models\CompanyInfo;
use App\Models\Inventory\RequestItem;
use App\Models\Inventory\RequestItemDetail;
use App\Models\Inventory\ProductExpenditureDetail;
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
                'Unit Pemakai'   => $data->user_unit ? $data->user_unit->name : '-',
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

    public function detail(Request $request)
    {
        $from_date = $request->from_date;
    	$to_date = $request->to_date;
    	$branch = $request->branch;
    	$type = $request->type;

        if(is_null($from_date) || is_null($to_date)){
            $to_date = $to_date ? $to_date : date('Y-m-d');
            $from_date = $from_date ? $from_date :date('Y-m-d', strtotime($to_date. '-10 months'));
        }
        $data = RequestItemDetail::select('id','request_item_id','product_id','unit_id','qty')
            ->with(
                'request_item:id,branch_id,number_spb,date_spb',
                'request_item.branch:id,name',
                'product:id,register_number,name,second_name',
                'unit:id,name'
            )
            ->withCount([
                'expenditure_detail AS qty_bpb' => function ($query) {
                    $query->select(DB::raw("SUM(qty) as qty"));
                }
            ])
            ->whereHas('request_item', function ($query) use ($type,$branch,$from_date,$to_date){
                $query->when($type, function ($query) use ($type){
                    if($type != 'all'){
                        $query->where('bpb_type_id',$type);
                    }
                })
                ->when($branch, function ($query) use ($branch){
                    $query->whereHas('branch',function ($q) use ($branch){
                        $q->where('branches.id',$branch);
                    });
                })
                ->whereDate('date_spb','>=',$from_date)
                ->whereDate('date_spb','<=',$to_date);
            })->paginate(10);
        return response()->json($data);
    }

    public function detail_item(Request $request,$id)
    {
        $data = ProductExpenditureDetail::select('id','product_expenditure_id','product_status_id','qty')
            ->with(
                'product_expenditure:id,warehouse_id,number_bpb,date_bpb',
                'product_expenditure.warehouse:id,name',
                'product_status:id,name',
            )->where('request_item_detail_id',$id)->paginate(10);
        return response()->json($data);
    }
}
