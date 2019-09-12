<?php

namespace App\Http\Controllers\Admin\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Request\Status as modelRequestStatus;
use App\Models\Network as modelNetwork;
use App\Models\User as modelUser;
use App\Models\EmailTemplate as modelEmailTemplate;

use DB;
use DateTime;
use App\Services\Mailer;
use App\Services\EverFlow\Offer as EF_Offer;

class StatusController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:change_offer_change_status'], ['only' => ['index', 'ajax-get', 'add', 'save-add', 'edit', 'save-edit']]);
        $this->middleware(['permission:todo_status_change'], ['only' => ['index', 'ajax-get', 'edit', 'save-edit', 'view', 'decline', 'save-decline', 'approve']]);
    }


    public function index()
    {
        $model = new modelRequestStatus();
        $modelUser = new modelUser();

        $dataManager = $modelUser->getManager();

        return view('admin.request.status.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
            'dataStatus' => $model->arrStatus,

        ]);
    }


    public function ajaxGet(Request $request)
    {

        $columns     = ['rs.id', 'o.lt_id', 'o.ef_id', 'o.campaign_name', 'rs.lt_status', 'rs.ef_status', 'rs.date', 'rs.status', 'u.name', 'rs.created_at'];
        $columnsLike = ['rs.id', 'o.lt_id', 'o.ef_id', 'o.campaign_name', 'rs.lt_status', 'rs.ef_status', DB::raw('DATE_FORMAT(rs.date, "%b %e, %Y")'), 'rs.status', DB::raw('IF(rs.created_by_id > 0, u.name, rs.created_by)'), DB::raw('DATE_FORMAT(o.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('request_status AS rs')
            ->select('rs.id', 'o.lt_id', 'o.ef_id', 'o.campaign_name', 'rs.lt_status', 'rs.ef_status', DB::raw('DATE_FORMAT(rs.date, "%b %e, %Y") AS date'), 'rs.status', DB::raw('IF(rs.created_by_id > 0, u.name, rs.created_by) AS created_name'), DB::raw('DATE_FORMAT(rs.created_at, "%b %e, %Y %T") AS created_at'))
            ->join('offer AS o', 'o.id', '=', 'rs.offer_id')
            ->leftJoin('users AS u', 'u.id', '=', 'rs.created_by_id')
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
            $query->where('rs.created_by_id', $request->created_by);
        }
        if($request->status){
            $query->where('rs.status', $request->status);
        }

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('request_status')->count(),
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

        $dataNetwork = $modelNetwork->getNetwork();

        return view('admin.request.status.add', [
            'dataNetwork' => $dataNetwork,
        ]);
    }


    public function edit($id)
    {
        $modelNetwork = new modelNetwork();

        $data = modelRequestStatus::where('id', $id)->whereIn('status', [1,2])->first();
        if(!$data){

            return redirect()->route('admin.request.status.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Status Change Request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $dataNetwork = $modelNetwork->getNetwork();

        return view('admin.request.status.edit', [
            'data' => $data,
            'dataOffer' => $data->offer,
            'dataNetwork' => $dataNetwork,
        ]);
    }


    public function saveAdd(Request $request)
    {
        $this->validate($request, [
            'offer_id' => 'required|integer',
            'date' => 'required|date',
            'lt_new_status' => 'required_with:linktrust|max:31|nullable',
            'ef_new_status' => 'required_with:everflow|max:31|nullable',
            'mass_notice' => 'required|integer',
            'redirect_url' => 'nullable|url',
            'reason' => 'required',
            'tracking_platform' => 'required_without_all:linktrust,everflow',
        ]);

        $auth = Auth::user();

        $tmp = explode("/", $request->date);
        $dateTime = new DateTime();
        $dateTime->setDate($tmp[2], $tmp[0], $tmp[1]);

        $data = new modelRequestStatus();
        $data->fill([
            'offer_id' => $request->offer_id,
            'date' => $dateTime->format('Y-m-d'),
            'need_api_lt' => $request->linktrust ? 1 : 0,
            'need_api_ef' => $request->everflow ? 1 : 0,
            'lt_status' => $request->lt_new_status ? : null,
            'ef_status' => $request->ef_new_status ? : null,
            'mass_notice' => $request->mass_notice ? 1 : 0,
            'redirect_url' => $request->redirect_url,
            'reason' => $request->reason,
            'status' => 1,
            'created_by' => $auth->email,
            'created_by_id' => $auth->id,
        ]);

        $data->save();

        $mailer = new Mailer();
        $mailer->sendNewRequestStatus($data);

        return redirect()->route('admin.request.status.add')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "New Status Change Request has been created !",
            'autohide' => 1,
        ]]);
    }


    public function saveEdit(Request $request)
    {
        $this->validate($request, [
            'offer_id' => 'required|integer',
            'date' => 'required|date',
            'lt_new_status' => 'required_with:linktrust|max:31|nullable',
            'ef_new_status' => 'required_with:everflow|max:31|nullable',
            'mass_notice' => 'required|integer',
            'redirect_url' => 'nullable|url',
            'reason' => 'required',
            'tracking_platform' => 'required_without_all:linktrust,everflow',
        ]);

        $data = modelRequestStatus::where('id', $request->id)->whereIn('status', [1,2])->first();
        if(!$data){

            return redirect()->route('admin.request.status.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Status Change Request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $tmp = explode("/", $request->date);
        $dateTime = new DateTime();
        $dateTime->setDate($tmp[2], $tmp[0], $tmp[1]);

        $data->fill([
            'offer_id' => $request->offer_id,
            'date' => $dateTime->format('Y-m-d'),
            'need_api_lt' => $request->linktrust ? 1 : 0,
            'need_api_ef' => $request->everflow ? 1 : 0,
            'lt_status' => $request->lt_new_status ? : null,
            'ef_status' => $request->ef_new_status ? : null,
            'mass_notice' => $request->mass_notice ? 1 : 0,
            'redirect_url' => $request->redirect_url,
            'reason' => $request->reason,
        ]);

        $data->save();

        return redirect()->route('admin.request.status.index')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Status Change Request has been updated !",
            'autohide' => 1,
        ]]);
    }


    public function view($id)
    {
        $data = modelRequestStatus::where('id', $id)->first();
        if($data){

            return view('admin.request.status.view', [
                'data' => $data,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Status change request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function decline($id)
    {
        $data = modelRequestStatus::where('id', $id)->whereIn('status', [1])->first();
        if($data){

            $dataTemplate = modelEmailTemplate::where('name', 'request_offer_status_declined')->where('status', 3)->first();
            if(!$dataTemplate){
                throw new Exception('Error: Email Template not found !');
            }

            return view('admin.request.status.decline', [
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

        $data = modelRequestStatus::where('id', $request->id)->whereIn('status', [1])->first();
        if($data){

            $data->status = 2;
            $data->save();

            $mailer = new Mailer();
            $mailer->sendDeclineRequestStatus($data, $request->reason);

            $alert = [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Status change request has been declined.",
                'hide' => 1,
            ];

            return view('admin.request.status.decline', ['status' => 'success', 'alert' => $alert, 'data' => $data]);

        } else {

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Status change request not fount, please try again !",
                'hide' => 0,
            ];

            return view('admin.request.status.decline', ['status' => 'error', 'alert' => $alert, 'data' => $data]);
        }
    }


    public function approve($id)
    {
        $data = modelRequestStatus::where('id', $id)->whereIn('status', [1, 2])->first();
        if($data){

            $data->status = 4;
            $data->save();

            return redirect()->route('admin.request.status.index')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Status change request (id: $data->id) has been pushed to cron.",
                'autohide' => 0,
            ]]);


        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Status change request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }
}
