<?php

namespace App\Http\Controllers\Admin\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use DB;

use App\Models\Network as modelNetwork;
use App\Models\CapType as modelCapType;
use App\Models\CapUnit as modelCapUnit;
use App\Models\Request\Cap as modelRequestCap;
use App\Models\User as modelUser;
USE App\Models\EmailTemplate as modelEmailTemplate;

use App\Services\Mailer;
use App\Services\EverFlow\Offer as EF_Offer;
use DateTime;
use Validator;

class CapController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:request_cap_request'], ['only' => ['index', 'ajax-get', 'add', 'save-add', 'edit', 'save-edit']]);
        $this->middleware(['permission:todo_cap_change'], ['only' => ['index', 'ajax-get', 'edit', 'save-edit', 'view', 'decline', 'save-decline', 'approve']]);
    }


    public function index()
    {
        $model = new modelRequestCap();
        $modelUser = new modelUser();

        $dataManager = $modelUser->getManager();

        $dataCapType = modelCapType::all();
        $keyCapType = [];
        foreach($dataCapType as $iter){
            $keyCapType[$iter->id] = $iter;
        }

        return view('admin.request.cap.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
            'dataStatus' => $model->arrStatus,
            'dataCapType' => $keyCapType,
        ]);
    }


    public function ajaxGet(Request $request)
    {

        $columns     = ['rc.id', 'o.lt_id', 'o.ef_id', 'o.campaign_name', 'rc.cap', 'rc.cap_type_id', 'rc.date', 'rc.status', 'rc.error_cron', 'u.name', 'rc.created_at'];
        $columnsLike = ['rc.id', 'o.lt_id', 'o.ef_id', 'o.campaign_name', 'rc.cap', 'rc.cap_type_id', DB::raw('DATE_FORMAT(rc.date, "%b %e, %Y")'), 'rc.status', 'rc.error_cron', DB::raw('IF(rc.created_by_id > 0, u.name, rc.created_by)'), DB::raw('DATE_FORMAT(rc.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('request_cap AS rc')
            ->select('rc.id', 'o.lt_id', 'o.ef_id', 'o.campaign_name', 'rc.cap', 'rc.cap_type_id', DB::raw('DATE_FORMAT(rc.date, "%b %e, %Y") AS date'), 'rc.status', 'rc.error_cron', DB::raw('IF(rc.created_by_id > 0, u.name, rc.created_by) AS created_name'), DB::raw('DATE_FORMAT(rc.created_at, "%b %e, %Y %T") AS created_at'))
            ->join('offer AS o', 'o.id', '=', 'rc.offer_id')
            ->leftJoin('users AS u', 'u.id', '=', 'rc.created_by_id')
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

        if($request->created_by){
            $query->where('rc.created_by_id', $request->created_by);
        }
        if($request->status){
            $query->where('rc.status', $request->status);
        }

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('request_cap')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function add()
    {
        $modelNetwork = new modelNetwork();
        $modelCapType = new modelCapType();

        $dataNetwork = $modelNetwork->getNetwork();
        $dataCapType = $modelCapType->getType();

        return view('admin.request.cap.add', [
            'dataNetwork' => $dataNetwork,
            'dataCapType' => $dataCapType,
        ]);
    }


    public function edit($id)
    {

        $modelNetwork = new modelNetwork();
        $modelCapType = new modelCapType();

        $data = modelRequestCap::where('id', $id)->whereIn('status', [1,2])->first();
        if(!$data){

            return redirect()->route('admin.request.cap.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Cap request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $dataNetwork = $modelNetwork->getNetwork();
        $dataCapType = $modelCapType->getType();

        return view('admin.request.cap.edit', [
            'data' => $data,
            'dataOffer' => $data->offer,
            'dataNetwork' => $dataNetwork,
            'dataCapType' => $dataCapType,
        ]);
    }


    public function saveAdd(Request $request)
    {
        $this->validate($request, [
            'offer_id' => 'required|array',
            'date' => 'required|date',
            'cap' => 'required|integer',
            'cap_type_id' => 'required|integer',
            'cap_reset' => 'required|integer',
            'redirect_url' => 'nullable|url',
            'reason' => 'required',
        ]);

        $auth = Auth::user();

        $tmp = explode("/", $request->date);
        $dateTime = new DateTime();
        $dateTime->setDate($tmp[2], $tmp[0], $tmp[1]);

        if($request->offer_id){
            foreach($request->offer_id as $key => $offer_id){

                $data = new modelRequestCap();
                $data->fill([
                    'offer_id' => $offer_id,
                    'date' => $dateTime->format('Y-m-d'),
                    'cap' => $request->cap,
                    'cap_type_id' => $request->cap_type_id,
                    'cap_reset' => $request->cap_reset ? 1 : 0,
                    'redirect_url' => $request->redirect_url,
                    'reason' => $request->reason,
                    'status' => 1,
                    'created_by' => $auth->email,
                    'created_by_id' => $auth->id,
                ]);

                $data->save();

                $mailer = new Mailer();
                $mailer->sendNewRequestCap($data);
            }
        }

        return redirect()->route('admin.request.cap.add')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "New Cap Request has been created !",
            'autohide' => 1,
        ]]);
    }


    public function saveEdit(Request $request)
    {
        $this->validate($request, [
            'offer_id' => 'required|integer',
            'date' => 'required|date',
            'cap' => 'required|integer',
            'cap_type_id' => 'required|integer',
            'cap_reset' => 'required|integer',
            'redirect_url' => 'nullable|url',
            'reason' => 'required',
        ]);

        $data = modelRequestCap::where('id', $request->id)->whereIn('status', [1,2])->first();
        if(!$data){

            return redirect()->route('admin.request.cap.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Cap request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $tmp = explode("/", $request->date);
        $dateTime = new DateTime();
        $dateTime->setDate($tmp[2], $tmp[0], $tmp[1]);

        $data->fill([
            'offer_id' => $request->offer_id,
            'date' => $dateTime->format('Y-m-d'),
            'cap' => $request->cap,
            'cap_type_id' => $request->cap_type_id,
            'cap_reset' => $request->cap_reset ? 1 : 0,
            'redirect_url' => $request->redirect_url,
            'reason' => $request->reason,
        ]);

        $data->save();

        return redirect()->route('admin.request.cap.index')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Cap Request has been updated !",
            'autohide' => 1,
        ]]);
    }


    public function view($id)
    {
        $data = modelRequestCap::where('id', $id)->first();
        if($data){

            return view('admin.request.cap.view', [
                'data' => $data,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Cap request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function decline($id)
    {
        $data = modelRequestCap::where('id', $id)->whereIn('status', [1])->first();
        if($data){

            $dataTemplate = modelEmailTemplate::where('name', 'request_cap_decline')->where('status', 3)->first();
            if(!$dataTemplate){
                throw new Exception('Error: Email Template not found !');
            }

            return view('admin.request.cap.decline', [
                'data' => $data,
                'dataTemplate' => $dataTemplate,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Cap request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function saveDecline(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'reason' => 'required',
        ]);

        $data = modelRequestCap::where('id', $request->id)->whereIn('status', [1])->first();
        if($data){

            $data->status = 2;
            $data->save();

            $mailer = new Mailer();
            $mailer->sendDeclineCapRequest($data, $request->reason);

            $alert = [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Cap request has been declined.",
                'hide' => 1,
            ];

            return view('admin.request.cap.decline', ['status' => 'success', 'alert' => $alert, 'data' => $data]);

        } else {

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Cap request not fount, please try again !",
                'hide' => 0,
            ];

            return view('admin.request.cap.decline', ['status' => 'error', 'alert' => $alert, 'data' => $data]);
        }
    }


    public function approve($id)
    {
        $data = modelRequestCap::where('id', $id)->whereIn('status', [1, 2])->first();
        if($data){

            $data->status = 4;
            $data->save();

            return redirect()->route('admin.request.cap.index')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Cap request (id: $data->id) has been pushed to cron.",
                'autohide' => 0,
            ]]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Cap request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }
}