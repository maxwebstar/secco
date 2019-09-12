<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\User as modelUser;

use DB;

class PrePayController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:report_pre_pay'], ['only' => ['index', 'ajax-get', 'csv-export']]);
    }

    public function index()
    {
        $modelUser = new modelUser();

        $dataManager = $modelUser->getManager();

        return view('admin.prepay.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
        ]);

    }

    public function ajaxGet(Request $request)
    {
        $columns     = ['a.id', 'a.lt_id', 'a.ef_id', 'a.name', 'pp.amount', 'pp.revenue', 'pp.revenue_mtd', 'pp.balance_remaining', 'pp.used_percent'];
        $columnsLike = ['a.id', 'a.lt_id', 'a.ef_id', 'a.name', 'pp.amount', 'pp.revenue', 'pp.revenue_mtd', 'pp.balance_remaining', 'pp.used_percent'];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('pre_pay AS pp')
            ->select('a.id', 'a.lt_id', 'a.ef_id', 'a.name', 'pp.amount', 'pp.revenue', 'pp.revenue_mtd', 'pp.balance_remaining', 'pp.used_percent')
            ->join('advertiser AS a', 'a.id', '=', 'pp.advertiser_id')
            ->leftJoin('users AS u', 'u.id', '=', 'a.created_by_id')
            ->where('a.prepay', 1)
            ->orderBy($columns[$order], $dir);

        if($searchValue) {

            $query->where(function ($queryLike) use ($request, $columnsLike, $searchValue) {

                foreach ($columnsLike as $key => $name) {
                    if ($request->columns[$key]['searchable']) {
                        $queryLike->orWhere($name, 'like', "%$searchValue%");
                    }
                }
            });
        }

        if($request->manager){
            $query->where('a.manager_id', $request->manager);
        }
        /*
        if($request->status){
            $query->where('rc.status', $request->status);
        }*/

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('pre_pay')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function csvExport()
    {
        $dataAuth = Auth::user();

        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('pre_pay AS pp')
            ->select('a.id', 'a.lt_id', 'a.ef_id', 'a.name', 'pp.amount', 'pp.revenue_mtd', 'pp.revenue', 'pp.balance_remaining', 'pp.used_percent')
            ->join('advertiser AS a', 'a.id', '=', 'pp.advertiser_id')
            ->leftJoin('users AS u', 'u.id', '=', 'a.created_by_id')
            ->where('a.prepay', 1)
            ->orderBy("a.name", "ASC");


        if($dataAuth->hasRole('hasRole')){
            $query->where('a.manager_id', $dataAuth->id);
        }

        $result = "ID, LT ID, EF ID, Advertiser Name, Payments, MTD Revenue, Revenue, Prepay Remaining, Used(%)\n";

        $query->chunk(100, function ($arr) use (&$result) {
            foreach ($arr as $iter) {
                $result .= "$iter->id, $iter->lt_id, $iter->ef_id, $iter->name, $iter->amount, $iter->revenue_mtd, $iter->revenue, $iter->balance_remaining, $iter->used_percent\n";
            }
        });

        return response($result)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=prepay.csv')
            ->header('X-Header-Two', 'no-cache')
            ->header('Expires', '0');
    }
}
