<?php

namespace App\Http\Controllers\Admin\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\User as modelUser;
use App\Models\Request\Statistic as modelRequestStatistic;

use DB;
use Exception;
use PDOException;
use Validator;
use Carbon\Carbon;

class StatisticController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:advertiser_list_stat_request'], ['only' => ['index', 'ajax-get']]);
        $this->middleware(['permission:advertiser_statrequest'], ['only' => ['ajax-save', 'ajax-save-notification']]);
    }


    public function index()
    {
        $modelUser = new modelUser();
        $model = new modelRequestStatistic();

        $dataManager = $modelUser->getManager();

        $sqlDB = DB::connection('mysql');
        $dataFromUser = $sqlDB->table('users AS u')
            ->select('u.id', 'u.name', DB::raw('r.name as role_name'), 'u.email')
            ->join('role_user AS ru', 'ru.user_id', '=', 'u.id')
            ->join('roles AS r', 'r.id', '=', 'ru.role_id')
            ->whereIn('r.name', ['admin', 'accounting', 'account_manager'])
            ->whereNotNull('google_token')
            ->get();

        $dataFromUserKey = [];
        foreach($dataFromUser as $iter){
            $dataFromUserKey[$iter->id] = $iter->name;
        }

        return view('admin.request.statistic.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
            'dataFromUser' => $dataFromUser,
            'dataFromUserKey' => $dataFromUserKey,
        ]);
    }


    public function ajaxGet(Request $request)
    {

        $columns     = ['a.id', 'a.lt_id', 'a.ef_id', 'a.name', DB::raw('IFNULL(rs.advertiser_contact, a.contact)'), 'a.email', 'rs.advertiser_email', DB::raw('SUM(as.revenue)'), DB::raw('SUM(as.click)'), 'rs.from_user_id', 'rs.notification', 'rs.reason'];
        $columnsLike = ['a.id', 'a.lt_id', 'a.ef_id', 'a.name', DB::raw('IFNULL(rs.advertiser_contact, a.contact)'), 'a.email', 'rs.advertiser_email', 'rs.from_user_id', 'rs.notification', 'rs.reason'];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value

        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('advertiser AS a')
            ->select('a.id', 'a.lt_id', 'a.ef_id', 'a.name', DB::raw('IFNULL(rs.advertiser_contact, a.contact) as contact'), 'rs.advertiser_email', 'a.email', DB::raw('SUM(as.revenue) as revenue'), DB::raw('SUM(as.click) as click'), 'rs.from_user_id', 'rs.notification', 'rs.reason')
            ->join('advertiser_stat AS as', 'as.advertiser_id', '=', 'a.id')
            ->leftJoin('request_statistic AS rs', 'rs.advertiser_id', '=', 'a.id')
            ->where(DB::raw('DATE_FORMAT(as.date, "%Y-%m")'), date("Y-m", strtotime($request->date)))
            ->where(function($query) use ($request){
                $query->where('as.revenue', '>', 0)
                    ->orWhere('as.click', '>', 0);
            })
            ->groupBy('a.id')
            ->orderBy($columns[$order], $dir);

        $queryTotal = $query;
        $total = 0;
        $queryTotal->chunk(100, function ($arr) use (&$total) {
            foreach ($arr as $iter) {
                $total ++;
            }
        });

        if($searchValue) {

            $query->where(function ($queryLike) use ($request, $columnsLike, $searchValue) {

                foreach ($columnsLike as $key => $name) {
                    if ($request->columns[$key]['searchable']) {
                         $queryLike->orWhere($name, 'like', "%$searchValue%");
                    }
                }
            });
        }

        if($request->manager_id){
            $query->where('a.manager_id', $request->manager_id);
        }
        if($request->from_user_id){
            $query->where('rs.from_user_id', $request->from_user_id);
        }
        if($request->notified){
            $query->where('rs.notification', 1);
        }

        $queryFilter = $query;
        $totalFilter = 0;
        $queryFilter->chunk(100, function ($arr) use (&$totalFilter) {
            foreach ($arr as $iter) {
                $totalFilter ++;
            }
        });

        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $total,
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function ajaxSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'field' => 'required|max:255',
            'value' => 'nullable|max:255',
        ]);

        if ($validator->fails()) {

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $validator->errors()->first(),
                'hide' => 0,
            ];

            return response()->json(['status' => 'error', 'msg' => 'Failed to validate', 'alert' => $alert]);
        }

        $auth = Auth::user();

        try {

            $data = modelRequestStatistic::where('advertiser_id', $request->id)->first();
            if ($data) {
                $data->updated_by_id = $auth->id;
            } else {
                $data = new modelRequestStatistic();
                $data->advertiser_id = $request->id;
                $data->created_by_id = $auth->id;
            }

            $data->{$request->field} = $request->value ? : null;
            $data->save();

            $status = 'success';
            $alert = [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Data for $request->field has been saved !",
                'hide' => 1,
            ];

            return response()->json(['status' => $status, 'alert' => $alert]);

        } catch (PDOException $e) {

            $status = 'error';
            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'hide' => 0,
            ];

        } catch (Exception $e) {

            $status = 'error';
            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'hide' => 0,
            ];
        }

        return response()->json(['status' => $status, 'msg' => 'Failed to save', 'alert' => $alert]);
    }


    public function ajaxSaveNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'from_user_id' => 'nullable|integer',
            'notification' => 'required|integer',
            'reason' => 'required_if:notification,0',
        ]);

        if($validator->fails()){

            return response()->json(['status' => 'not_valid', 'param' => $validator->errors()]);
        }

        $auth = Auth::user();

        try {

            $data = modelRequestStatistic::where('advertiser_id', $request->id)->first();
            if ($data) {
                $data->updated_by_id = $auth->id;
            } else {
                $data = new modelRequestStatistic();
                $data->advertiser_id = $request->id;
                $data->created_by_id = $auth->id;
            }

            $data->from_user_id = $request->from_user_id ? : 0;
            $data->notification = $request->notification;
            $data->reason = $request->reason;
            $data->save();

            $advertiserName = $data->advertiser->name;

            $status = 'saved';
            $alert = [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Data for $advertiserName has been saved !",
                'hide' => 1,
            ];

            return response()->json(['status' => $status, 'alert' => $alert]);

        } catch (PDOException $e) {

            $status = 'error';
            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'hide' => 0,
            ];

        } catch (Exception $e) {

            $status = 'error';
            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'hide' => 0,
            ];
        }

        return response()->json(['status' => $status, 'alert' => $alert]);
    }


//    public function ajaxSaveSend(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'id' => 'required|integer',
//            'notification' => 'required|integer',
//        ]);
//
//        if($validator->fails()){
//
//            $alert = [
//                'type' => 'danger',
//                'title' => 'Error!',
//                'message' => $validator->errors()->first(),
//                'hide' => 0,
//            ];
//
//            return response()->json(['status' => 'error', 'alert' => $alert]);
//        }
//
//        $auth = Auth::user();
//
//        try {
//
//            $data = modelRequestStatistic::where('advertiser_id', $request->id)->first();
//            if ($data) {
//                $data->updated_by_id = $auth->id;
//            } else {
//                $data = new modelRequestStatistic();
//                $data->advertiser_id = $request->id;
//                $data->created_by_id = $auth->id;
//            }
//
//            $data->notification = $request->notification;
//            $data->save();
//
//            $advertiserName = $data->advertiser->name;
//
//            $status = 'saved';
//            $alert = [
//                'type' => 'success',
//                'title' => 'Success!',
//                'message' => "Data for $advertiserName has been saved !",
//                'hide' => 1,
//            ];
//
//            return response()->json(['status' => $status, 'alert' => $alert]);
//
//        } catch (PDOException $e) {
//
//            $status = 'error';
//            $alert = [
//                'type' => 'danger',
//                'title' => 'Error!',
//                'message' => $e->getMessage(),
//                'hide' => 0,
//            ];
//
//        } catch (Exception $e) {
//
//            $status = 'error';
//            $alert = [
//                'type' => 'danger',
//                'title' => 'Error!',
//                'message' => $e->getMessage(),
//                'hide' => 0,
//            ];
//        }
//
//        return response()->json(['status' => $status, 'alert' => $alert]);
//    }

}
