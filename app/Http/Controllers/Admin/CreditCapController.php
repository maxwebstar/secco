<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\User as modelUser;
use App\Models\Currency as modelCurrency;
use App\Models\QB\Report as modelQBReport;

use App\Services\QB\Core as QB_Core;

use DB;

class CreditCapController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:report_credit_cap'], ['only' => ['index', 'ajax-get']]);
        $this->middleware(['permission:report_qb_report'], ['only' => ['report', 'ajax-get-report']]);
        $this->middleware(['permission:report_qb_report_month'], ['only' => ['report-month', 'ajax-get-report-month']]);
    }

    public function index()
    {
        $modelUser = new modelUser();

        $dataManager = $modelUser->getManager();

        return view('admin.creditcap.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
        ]);

    }

    public function ajaxGet(Request $request)
    {
        $columns     = ['a.id', 'a.lt_id', 'a.ef_id', 'a.name', 'cc.ar', 'cc.revenue_mtd', 'cc.balance', 'cc.cap', 'cc.cap_percent'];
        $columnsLike = ['a.id', 'a.lt_id', 'a.ef_id', 'a.name', 'cc.ar', 'cc.revenue_mtd', 'cc.balance', 'cc.cap', 'cc.cap_percent'];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('credit_cap AS cc')
            ->select('a.id', 'a.lt_id', 'a.ef_id', 'a.name', 'cc.ar', 'cc.revenue_mtd', 'cc.balance', 'cc.cap', 'cc.cap_percent', 'cc.cap_type', 'cc.is_6_month', 'cc.num_month')
            ->join('advertiser AS a', 'a.id', '=', 'cc.advertiser_id')
            ->leftJoin('users AS u', 'u.id', '=', 'a.created_by_id')
            ->where('cc.cap', '>', 0)
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
            "recordsTotal"    => $sqlDB->table('credit_cap')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function report()
    {
        $modelUser = new modelUser();
        $modelCurrency = new modelCurrency();

        $dataManager = $modelUser->getManager();
        $dataCurrency = $modelCurrency->getAllCurrencyKeyID("sign");

        return view('admin.creditcap.report', [
            'auth' => Auth::user(),
            'data' => new modelQBReport(),
            'dataManager' => $dataManager,
            'dataCurrency' => $dataCurrency,
        ]);
    }


    public function reportMonth()
    {
        $modelUser = new modelUser();

        $dataManager = $modelUser->getManager();

        return view('admin.creditcap.reportmonth', [
            'auth' => Auth::user(),
            'data' => new modelQBReport(),
            'dataManager' => $dataManager,
        ]);
    }


    public function ajaxGetReport(Request $request)
    {
        $columns     = ['qr.id', 'qr.quickbook_id', 'qr.qb_number', 'a.name', 'qr.amount', 'qr.type', 'qr.date'];
        $columnsLike = ['qr.id', 'qr.quickbook_id', 'qr.qb_number', 'a.name', 'qr.amount', 'qr.type', DB::raw('DATE_FORMAT(qr.date, "%b %e, %Y")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('qb_advertiser_report AS qr')
            ->select('qr.id', 'qr.quickbook_id', 'qr.qb_number', DB::raw('a.name as advertiser_name'), 'qr.amount', 'qr.type', DB::raw('DATE_FORMAT(qr.date, "%b %e, %Y") as date'), 'qr.currency_id')
            ->leftJoin('advertiser AS a', 'a.id', '=', 'qr.advertiser_id')
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

        if($request->date){
            $query->where(DB::raw('DATE_FORMAT(qr.date, "%Y-%m")'), date("Y-m", strtotime($request->date)));
        }

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('qb_advertiser_report')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function ajaxGetReportMonth(Request $request)
    {
        $columns     = ['qr.id', 'a.name', 'qr.amount', 'qr.type', 'qr.date'];
        $columnsLike = ['qr.id', 'a.name', 'qr.amount', 'qr.type', DB::raw('DATE_FORMAT(qr.date, "%b %Y")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value

        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('qb_advertiser_report_mounth AS qr')
            ->select('qr.id', DB::raw('a.name as advertiser_name'), 'qr.amount', 'qr.type', DB::raw('DATE_FORMAT(qr.date, "%b %Y") as date'))
            ->leftJoin('advertiser AS a', 'a.id', '=', 'qr.advertiser_id')
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

        if($request->date){
            $query->where(DB::raw('DATE_FORMAT(qr.date, "%Y-%m")'), date("Y-m", strtotime($request->date)));
        }

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('qb_advertiser_report_mounth')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function ajaxCheck(Request $request)
    {
        $data = modelQBReport::where('id', $request->id)->first();
        if($data && $data->quickbook_id){

            if($data->type == 1 || $data->type == 2){

                $type = $data->getType();

                $qbCore = new QB_Core();
                $dataService = $qbCore->getDataService();

                if($data->type == 1){
                    $dataQB = $dataService->FindbyId('invoice', $data->quickbook_id);
                } else if($data->type == 2) {
                    $dataQB = $dataService->FindbyId('payment', $data->quickbook_id);
                }

                if($dataQB){
                    $alert = [
                        'type' => 'success',
                        'title' => 'Success!',
                        'message' => "$type exist in QB",
                        'hide' => 1,
                    ];
                    return response()->json(['status' => 'exist', 'alert' => $alert], 200);
                } else {
                    $alert = [
                        'type' => 'success',
                        'title' => 'Success!',
                        'message' => "$type not exist in QB",
                        'hide' => 1,
                    ];

                    $data->delete();

                    return response()->json(['status' => 'not_exist', 'alert' => $alert], 200);
                }

            } else {
                $alert = [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => "Invalid type for report data !",
                    'hide' => 1,
                ];
                return response()->json(['status' => 'invalid_type', 'alert' => $alert], 200);
            }

        } else {
            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "QB data not found !",
                'hide' => 1,
            ];
            return response()->json(['status' => 'not_found', 'alert' => $alert], 200);
        }

    }


    public function listenApiQb(Request $request)
    {
        /*20179fff-f40b-45e5-a431-354680e7c320*/

        $param = json_decode($request->getContent());

        if(isset($param->eventNotifications)){
            foreach($param->eventNotifications as $notification){

                if(isset($notification->dataChangeEvent->entities)){
                    foreach($notification->dataChangeEvent->entities as $entitie){

                        if($entitie->name == "Invoice"){
                            if($entitie->operation == "Delete"){
                               $data = modelQBReport::where('quickbook_id', $entitie->id)->where('type', 1)->first();
                               if($data){
                                   $data->delete();
                               }
                            }
                        } else if($entitie->name == "Payment") {
                            if($entitie->operation == "Delete"){
                                $data = modelQBReport::where('quickbook_id', $entitie->id)->where('type', 2)->first();
                                if($data){
                                    $data->delete();
                                }
                            }
                        }
                    }
                }
            }
        }
        /*
        $sqlDB = DB::connection('mysql');
        $sqlDB->insert('insert into `test` (`data`) values (?)', [json_encode($request->headers->all())]);

        return response()->json(['success' => 'ok'], 200);*/
    }
}
