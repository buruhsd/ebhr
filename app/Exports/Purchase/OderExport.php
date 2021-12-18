<?php

namespace App\Exports\Purchase;

use App\Models\Purchase\PurchaseOrder;
use App\Exports\Purchase\Sheets\OderSheet;
use App\Exports\Purchase\Sheets\DetailOderSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OderExport implements WithMultipleSheets
{
    use Exportable;

    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $status = $this->params['status'];
        $branch = $this->params['branch'];
        $from_date = $this->params['from_date'];
        $to_date = $this->params['to_date'];
        $order_id = PurchaseOrder::when($status, function ($query) use ($status){
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
                ->whereDate('date_op','<=',$to_date)->pluck('id')->toArray();
        $sheets = [];
        $sheets[] = new OderSheet($this->params);
        $sheets[] = new DetailOderSheet($order_id);

        return $sheets;
    }
}
