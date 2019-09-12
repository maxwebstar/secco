<?php

namespace App\Http\Controllers\Admin\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use DB;

use App\Models\Request\MassAdjustment as modelRequestMassAdjustment;
use App\Models\Network as modelNetwork;
use App\Models\User as modelUser;
use App\Models\EmailTemplate as modelEmailTemplate;

use App\Services\Mailer;

use Exception;
use PDOException;


class MassAdjustmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:request_mass_adjustment'], ['only' => ['index', 'ajax-get', 'add', 'save-add', 'edit', 'save-edit']]);
        $this->middleware(['permission:todo_mass_adjustment'], ['only' => ['index', 'ajax-get', 'edit', 'save-edit', 'view', 'decline', 'save-decline', 'approve']]);
    }

    public function index()
    {
        $model = new modelRequestMassAdjustment();
        $modelUser = new modelUser();

        $dataManager = $modelUser->getManager();

        return view('admin.request.massadjustment.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
            'dataStatus' => $model->arrStatus,
            'dataType' => $model->arrType,
        ]);
    }

    public function ajaxGet(Request $request)
    {

        $columns     = ['rma.id', 'n.short_name', 'o.campaign_name', 'a.name', 'rma.date', 'rma.click', 'rma.qualified', 'rma.approved', 'rma.revenue', 'rma.commission', 'rma.type', 'rma.status', 'u.name', 'rma.created_at'];
        $columnsLike = ['rma.id', 'n.short_name', 'o.campaign_name', 'a.name',  DB::raw('DATE_FORMAT(rma.date, "%b %e, %Y")'), 'rma.click', 'rma.qualified', 'rma.approved', 'rma.revenue', 'rma.commission', 'rma.type', 'rma.status', DB::raw('IF(rma.created_by_id > 0, u.name, rma.created_by)'), DB::raw('DATE_FORMAT(rma.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('request_mass_adjustment AS rma')
            ->select('rma.id', 'n.short_name', 'o.campaign_name', DB::raw('a.name as affiliate_name'), DB::raw('DATE_FORMAT(rma.date, "%b %e, %Y") AS date'), 'rma.click', 'rma.qualified', 'rma.approved', 'rma.revenue', 'rma.commission', 'rma.type', 'rma.status', DB::raw('IF(rma.created_by_id > 0, u.name, rma.created_by) AS created_name'), DB::raw('DATE_FORMAT(rma.created_at, "%b %e, %Y %T") AS created_at'),
                DB::raw('o.lt_id as offer_lt_id'),
                DB::raw('o.ef_id as offer_ef_id'),
                DB::raw('a.lt_id as affiliate_lt_id'),
                DB::raw('a.ef_id as affiliate_ef_id'),
                DB::raw('rma.offer_id as offer_id'),
                DB::raw('rma.affiliate_id as affiliate_id'))
            ->leftJoin('network AS n', 'n.id', '=', 'rma.network_id')
            ->leftJoin('offer AS o', 'o.id', '=', 'rma.offer_id')
            ->leftJoin('affiliate AS a', 'a.id', '=', 'rma.affiliate_id')
            ->leftJoin('users AS u', 'u.id', '=', 'rma.created_by_id')
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
            $query->where('rma.created_by_id', $request->created_by);
        }
        if($request->status){
            $query->where('rma.status', $request->status);
        }

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('request_price')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }

    public function add()
    {
        $model = new modelRequestMassAdjustment();
        $modelNetwork = new modelNetwork();

        $dataNetwork = $modelNetwork->getNetwork();

        return view('admin.request.massadjustment.add', [
            'data' => $model,
            'dataNetwork' => $dataNetwork,
        ]);
    }

    public function edit($id)
    {
        $modelNetwork = new modelNetwork();

        $data = modelRequestMassAdjustment::where('id', $id)->whereIn('status', [1, 2])->first();
        if (!$data) {

            return redirect()->route('admin.request.mass.adjustment.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Mass adjustment request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $dataNetwork = $modelNetwork->getNetwork();

        $network = $data->network;
        $dataOffer = $data->offer;

        if (isset($dataOffer->{$network->field_name})) {
            $labelOffer = "(" . $dataOffer->{$network->field_name} . ") " . $dataOffer->campaign_name;
        } else {
            $labelOffer = $dataOffer->campaign_name;
        }

        $dataAffiliate = $data->affiliate;

        if (isset($dataOffer->{$network->field_name})) {
            $labelAffiliate = "(" . $dataOffer->{$network->field_name} . ") " . $dataAffiliate->name;
        } else {
            $labelAffiliate = $dataAffiliate->name;
        }

        return view('admin.request.massadjustment.edit', [
            'data' => $data,
            'dataType' => $data->arrType,
            'dataNetwork' => $dataNetwork,
            'labelOffer' => $labelOffer,
            'labelAffiliate' => $labelAffiliate,
        ]);
    }

    public function saveAdd(Request $request)
    {
        $this->validate($request, [
            'network_id' => 'required|integer',
            'offer_id' => 'required',
            'affiliate_id' => 'required',
            'date' => 'required|date',
            'click' => 'nullable|integer',
            'qualified' => 'nullable|integer',
            'approved' => 'nullable|integer',
            'revenue' => 'nullable|numeric',
            'commission' => 'required|numeric',
            'type' => 'nullable|integer',
            'reason' => 'required',
        ]);

        $auth = Auth::user();

        if($request->offer_id == "0000"){
            $offer_id = "0000";
        } else {
            $offer_id = $request->offer_id ? : 0;
        }

        if($request->affiliate_id == "0000"){
            $affiliate_id = "0000";
        } else if($request->affiliate_id == "000"){
            $affiliate_id = "000";
        } else {
            $affiliate_id = $request->affiliate_id ? : 0;
        }

        $data = new modelRequestMassAdjustment();
        $data->fill([
            'network_id' => $request->network_id,
            'offer_id' => $offer_id,
            'affiliate_id' => $affiliate_id,
            'date' => date('Y-m-d', strtotime($request->date)),
            'click' => $request->click ? : 0,
            'qualified' => $request->qualified ? : 0,
            'approved' => $request->approved ? : 0,
            'revenue' => $request->revenue ? : 0,
            'commission' => $request->commission ? : 0,
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 1,
            'created_by' => $auth->email,
            'created_by_id' => $auth->id,
        ]);

        $data->save();

        $mailer = new Mailer();
        $mailer->sendNewRequestMassAdjustment($data);

        return redirect()->route('admin.request.mass.adjustment.add')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "New Mass adjustment request has been created !",
            'autohide' => 1,
        ]]);
    }


    public function saveEdit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'network_id' => 'required|integer',
            'offer_id' => 'required',
            'affiliate_id' => 'required',
            'date' => 'required|date',
            'click' => 'nullable|integer',
            'qualified' => 'nullable|integer',
            'approved' => 'nullable|integer',
            'revenue' => 'nullable|numeric',
            'commission' => 'required|numeric',
            'type' => 'nullable|integer',
            'reason' => 'required',
        ]);

        $auth = Auth::user();

        $data = modelRequestMassAdjustment::where('id', $request->id)->first();
        if(!$data){

            return redirect()->route('admin.request.mass.adjustment.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Mass adjustment request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        if($request->offer_id == "0000"){
            $offer_id = "0000";
        } else {
            $offer_id = $request->offer_id ? : 0;
        }

        if($request->affiliate_id == "0000") {
            $affiliate_id = "0000";
        } else if($request->affiliate_id == "000"){
            $affiliate_id = "000";
        } else {
            $affiliate_id = $request->affiliate_id ? : 0;
        }

        $data->fill([
            'network_id' => $request->network_id,
            'offer_id' => $offer_id,
            'affiliate_id' => $affiliate_id,
            'date' => date('Y-m-d', strtotime($request->date)),
            'click' => $request->click ? : 0,
            'qualified' => $request->qualified ? : 0,
            'approved' => $request->approved ? : 0,
            'revenue' => $request->revenue ? : 0,
            'commission' => $request->commission ? : 0,
            'type' => $request->type,
            'reason' => $request->reason,
        ]);

        $data->save();

        return redirect()->route('admin.request.mass.adjustment.index')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Mass adjustment request has been updated !",
            'autohide' => 1,
        ]]);
    }


    public function view($id)
    {
        $data = modelRequestMassAdjustment::where('id', $id)->first();
        if($data){

            return view('admin.request.massadjustment.view', [
                'data' => $data,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Mass adjustment request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function decline($id)
    {
        $data = modelRequestMassAdjustment::where('id', $id)->whereIn('status', [1])->first();
        if($data){

            $dataTemplate = modelEmailTemplate::where('name', 'request_request_mass_adjustment_declined')->where('status', 3)->first();
            if(!$dataTemplate){
                throw new Exception('Error: Email Template not found !');
            }

            return view('admin.request.massadjustment.decline', [
                'data' => $data,
                'dataTemplate' => $dataTemplate,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Mass adjustment request not fount, please try again !",
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

        $data = modelRequestMassAdjustment::where('id', $request->id)->whereIn('status', [1])->first();
        if($data){

            $data->status = 2;
            $data->save();

            $mailer = new Mailer();
            $mailer->sendDeclineRequestMassAdjustment($data, $request->reason);

            $alert = [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Mass adjustment request has been declined.",
                'hide' => 1,
            ];

            return view('admin.request.massadjustment.decline', ['status' => 'success', 'alert' => $alert, 'data' => $data]);

        } else {

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Mass adjustment request not fount, please try again !",
                'hide' => 0,
            ];

            return view('admin.request.massadjustment.decline', ['status' => 'error', 'alert' => $alert, 'data' => $data]);
        }
    }


    public function approve($id)
    {
        $data = modelRequestMassAdjustment::where('id', $id)->whereIn('status', [1, 2])->first();
        if($data){

            $data->status = 3;
            $data->save();

            return redirect()->route('admin.request.mass.adjustment.index')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Mass adjustment request (id: $data->id) has been approved.",
                'autohide' => 0,
            ]]);


        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Mass adjustment request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }



}
