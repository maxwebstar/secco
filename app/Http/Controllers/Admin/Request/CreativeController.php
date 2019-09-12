<?php

namespace App\Http\Controllers\Admin\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use DB;

use App\Models\Network as modelNetwork;
use App\Models\CapType as modelCapType;
use App\Models\OfferCreative as modelOfferCreative;
use App\Models\OfferCreativeMissing as modelOfferCreativeMis;
use App\Models\Request\Creative as modelRequestCreative;
use App\Models\User as modelUser;
USE App\Models\EmailTemplate as modelEmailTemplate;

use Exception;
use PDOException;

use App\Services\Mailer;

class CreativeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:request_new_creative'], ['only' => ['index', 'ajax-get', 'add', 'save-add', 'edit', 'save-edit']]);
        $this->middleware(['permission:todo_creative_request'], ['only' => ['index', 'ajax-get', 'edit', 'save-edit', 'view', 'decline', 'save-decline', 'approve']]);
        $this->middleware(['permission:request_creative_missing'], ['only' => ['missing', 'view-missing', 'ignore-missing', 'add-missing', 'attach-missing', 'ajax-get-missing', 'save-attach-missing']]);
    }


    public function index()
    {
        $model = new modelRequestCreative();
        $modelOfferCreative = new modelOfferCreative();
        $modelUser = new modelUser();

        $dataManager = $modelUser->getManager();

        $dataCapType = modelCapType::all();
        $keyCapType = [];
        foreach($dataCapType as $iter){
            $keyCapType[$iter->id] = $iter;
        }

        return view('admin.request.creative.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
            'dataStatus' => $model->arrStatus,
            'dataCreativeStatus' => $model->arrStatus,
            'dataCapType' => $keyCapType,
        ]);
    }


    public function ajaxGet(Request $request)
    {

        $columns     = ['rc.id', 'rc.need_api_lt', 'rc.need_api_ef', 'o.campaign_name', 'rc.status', 'u.name', 'rc.created_at'];
        $columnsLike = ['rc.id', 'rc.need_api_lt', 'rc.need_api_ef', 'o.campaign_name', 'rc.status', DB::raw('IF(rc.created_by_id > 0, u.name, rc.created_by)'), DB::raw('DATE_FORMAT(rc.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('request_creative AS rc')
            ->select('rc.id', 'rc.need_api_lt', 'rc.need_api_ef', 'o.campaign_name', 'rc.status', DB::raw('IF(rc.created_by_id > 0, u.name, rc.created_by) AS created_name'), DB::raw('DATE_FORMAT(rc.created_at, "%b %e, %Y %T") AS created_at'))
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
            "recordsTotal"    => $sqlDB->table('request_creative')->count(),
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

        return view('admin.request.creative.add', [
            'dataNetwork' => $dataNetwork,
            'dataCapType' => $dataCapType,
        ]);
    }


    public function edit($id)
    {
        $modelNetwork = new modelNetwork();
        $modelCapType = new modelCapType();

        $data = modelRequestCreative::where('id', $id)->whereIn('status', [1,2])->first();
        if(!$data){

            return redirect()->route('admin.request.creative.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Creative request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $dataNetwork = $modelNetwork->getNetwork();
        $dataCapType = $modelCapType->getType();
        $dataCreative = $data->creatives()->whereIn('status', [1,2])->get();

        $creative_id = [];
        $creative_name = [];
        $creative_link = [];
        $creative_price_in = [];
        $creative_price_out = [];

        if($dataCreative){
            foreach($dataCreative as $iter){
                $creative_id[] = $iter->id;
                $creative_name[] = $iter->name;
                $creative_link[] = $iter->link;
                $creative_price_in[] = $iter->price_in;
                $creative_price_out[] = $iter->price_out;
            }
        }

        return view('admin.request.creative.edit', [
            'data' => $data,
            'dataOffer' => $data->offer,
            'dataCreative' => $data->creatives,
            'dataNetwork' => $dataNetwork,
            'dataCapType' => $dataCapType,
            'creative_id' => $creative_id,
            'creative_name' => $creative_name,
            'creative_link' => $creative_link,
            'creative_price_in' => $creative_price_in,
            'creative_price_out' => $creative_price_out,
        ]);
    }


    public function saveAdd(Request $request)
    {
        $this->validate($request, [
            'offer_id' => 'required|integer',

            'cap_type_id' => 'required|integer',
            'cap' => 'nullable|max:11',
            'type_traffic' => 'nullable|max:100',

            'creative_name' => 'nullable|array',
            'creative_link' => 'nullable|array',
            'creative_price_in' => 'nullable|array',
            'creative_price_out' => 'nullable|array',

            'tracking_platform' => 'required_without_all:linktrust,everflow',
            'lt_status' => 'required_with:linktrust|max:31',
            'ef_status' => 'required_with:everflow|max:31',

            'restrictions' => 'nullable',
            'demos' => 'nullable',
            'notes' => 'nullable',
        ]);

        $auth = Auth::user();

        DB::beginTransaction();

        try {

            $data = new modelRequestCreative();
            $data->fill([
                'offer_id' => $request->offer_id,
                'need_api_lt' => $request->linktrust ? 1 : 0,
                'need_api_ef' => $request->everflow ? 1 : 0,
                'cap' => $request->cap ? : null,
                'cap_type_id' => $request->cap_type_id,
                'type_traffic' => $request->type_traffic ? : null,
                'restrictions' => $request->restrictions ? : null,
                'demos' => $request->demos ? : null,
                'notes' => $request->notes ? : null,
                'status' => 1,
                'created_by' => $auth->email,
                'created_by_id' => $auth->id,
            ]);

            $data->save();

            if($request->creative_name && is_array($request->creative_name)){
                foreach($request->creative_name as $key => $creativeName){

                    $creativeLink = $request->creative_link[$key];
                    $creativePriceIn = $request->creative_price_in[$key];
                    $creativePriceOut = $request->creative_price_out[$key];

                    if($creativeName && $creativeLink){

                        $creative = new modelOfferCreative();
                        $creative->fill([
                            'offer_id' => $request->offer_id,
                            'request_id' => $data->id,
                            'iteration' => $key + 1,
                            'name' => $creativeName,
                            'link' => $creativeLink,
                            'price_in' => $creativePriceIn,
                            'price_out' => $creativePriceOut,
                            'status' => 1,
                        ]);
                        $creative->save();
                    }
                }
            }

            DB::commit();

        } catch (PDOException $e) {

            DB::rollBack();

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'autohide' => 0,
            ]]);

        } catch (Exception $e) {

            DB::rollBack();

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'autohide' => 0,
            ]]);
        }

        $mailer = new Mailer();
        $mailer->sendNewRequestCreative($data, $data->creatives);

        return redirect()->route('admin.request.creative.add')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "New Creative Request has been created !",
            'autohide' => 1,
        ]]);
    }


    public function saveEdit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'offer_id' => 'required|integer',

            'cap_type_id' => 'required|integer',
            'cap' => 'nullable|max:11',
            'type_traffic' => 'nullable|max:100',

            'creative_id' => 'nullable|array',
            'creative_name' => 'nullable|array',
            'creative_link' => 'nullable|array',
            'creative_price_in' => 'nullable|array',
            'creative_price_out' => 'nullable|array',

            'tracking_platform' => 'required_without_all:linktrust,everflow',
            'lt_status' => 'required_with:linktrust|max:31',
            'ef_status' => 'required_with:everflow|max:31',

            'restrictions' => 'nullable',
            'demos' => 'nullable',
            'notes' => 'nullable',
        ]);

        $auth = Auth::user();

        $data = modelRequestCreative::where('id', $request->id)->whereIn('status', [1,2])->first();
        if(!$data){

            return redirect()->route('admin.request.creative.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Creative request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $dataOld = $data;
        $dataCreativeOld = modelOfferCreative::where('request_id', $data->id)->get();

        DB::beginTransaction();

        try {

            $data->fill([
                'offer_id' => $request->offer_id,
                'need_api_lt' => $request->linktrust ? 1 : 0,
                'need_api_ef' => $request->everflow ? 1 : 0,
                'cap' => $request->cap ? : null,
                'cap_type_id' => $request->cap_type_id,
                'type_traffic' => $request->type_traffic ? : null,
                'restrictions' => $request->restrictions ? : null,
                'demos' => $request->demos ? : null,
                'notes' => $request->notes ? : null,
            ]);

            $data->save();

            if($request->creative_name && is_array($request->creative_name)){
                foreach($request->creative_name as $key => $creativeName){

                    $creativeId = $request->creative_id[$key];
                    $creativeLink = $request->creative_link[$key];
                    $creativePriceIn = $request->creative_price_in[$key];
                    $creativePriceOut = $request->creative_price_out[$key];

                    if($creativeName && $creativeLink){

                        if($creativeId){
                            $creative = modelOfferCreative::where('id', $creativeId)
                                ->where('request_id', $data->id)
                                ->whereIn('status', [1,2])
                                ->first();
                            if(!$creative){
                                continue;
                            }
                        } else {
                            $creative = new modelOfferCreative();
                        }
                        $creative->fill([
                            'offer_id' => $data->offer_id,
                            'request_id' => $data->id,
                            'iteration' => $key + 1,
                            'name' => $creativeName,
                            'link' => $creativeLink,
                            'price_in' => $creativePriceIn,
                            'price_out' => $creativePriceOut,
                            'status' => 1,
                        ]);
                        $creative->save();

                    }
                }
            }

            DB::commit();

        } catch (PDOException $e) {

            DB::rollBack();

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'autohide' => 0,
            ]]);

        } catch (Exception $e) {

            DB::rollBack();

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'autohide' => 0,
            ]]);
        }

        $mailer = new Mailer();
        $mailer->sendUpdateDeclineRequestCreative($data, $dataOld, $dataCreativeOld);

        return redirect()->route('admin.request.creative.index')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Creative Request has been updated !",
            'autohide' => 1,
        ]]);
    }


    public function view($id)
    {
        $data = modelRequestCreative::where('id', $id)->first();
        if($data){

            return view('admin.request.creative.view', [
                'data' => $data,
                'dataCreative' => $data->creatives,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Creative request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function decline($id)
    {
        $data = modelRequestCreative::where('id', $id)->whereIn('status', [1])->first();
        if($data){

            $dataTemplate = modelEmailTemplate::where('name', 'request_cap_decline')->where('status', 3)->first();
            if(!$dataTemplate){
                throw new Exception('Error: Email Template not found !');
            }

            return view('admin.request.creative.decline', [
                'data' => $data,
                'dataTemplate' => $dataTemplate,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Creative request not fount, please try again !",
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

        $data = modelRequestCreative::where('id', $request->id)->whereIn('status', [1])->first();
        if($data){

            $data->status = 2;
            $data->save();

            $mailer = new Mailer();
            $mailer->sendDeclineRequestCreative($data, $request->reason);

            $alert = [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Creative request has been declined.",
                'hide' => 1,
            ];

            return view('admin.request.creative.decline', ['status' => 'success', 'alert' => $alert, 'data' => $data]);

        } else {

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Creative request not fount, please try again !",
                'hide' => 0,
            ];

            return view('admin.request.creative.decline', ['status' => 'error', 'alert' => $alert, 'data' => $data]);
        }
    }


    public function approve($id)
    {
        $data = modelRequestCreative::where('id', $id)->whereIn('status', [1, 2])->first();
        if($data){
            $dataOffer = $data->offer;

            $offer_network_str = "";
//            if($data->need_api_lt){
//                $lt_Offer = new LT_Offer();
//                $lt_resp = $lt_Offer->createOffer($data);
//                if($lt_resp['lt_id']){
//                    $data->lt_id = $lt_resp['lt_id'];
//                    $offer_network_str .= " (LinkTrust id: $data->lt_id) ";
//                } else {
//                    return redirect()->route('admin.advertiser.add')->with(['message' => [
//                        'type' => 'danger',
//                        'title' => 'Error! LinkTrust Api',
//                        'message' => $lt_resp['message'],
//                        'autohide' => 0,
//                    ]]);
//                }
//            }
//            if($dataOffer->need_api_ef){
//                $ef_Offer = new EF_Offer();
//                $ef_resp = $ef_Offer->updateOfferCreative($dataOffer, $data);
//
//                if($ef_resp['ef_id']){
//                    $data->ef_id = $ef_resp['ef_id'];
//                    $offer_network_str .= " (EverFlow id: $data->ef_id) ";
//                } else {
//                    return redirect()->route('admin.request.creative.index')->with(['message' => [
//                        'type' => 'danger',
//                        'title' => 'Error! EverFlow Api',
//                        'message' => $ef_resp['message'],
//                        'autohide' => 0,
//                    ]]);
//                }
//            }

            $data->status = 3;
            $data->save();

            return redirect()->route('admin.request.creative.index')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Creative request (id: $data->id) has been approve. $offer_network_str",
                'autohide' => 0,
            ]]);


        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Creative request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function missing()
    {
        $modelUser = new modelUser();
        $model = new modelOfferCreativeMis();

        $dataManager = $modelUser->getManager();
        $dataManagerAccount = $modelUser->getManagerAccount();

        return view('admin.request.creative.missing', [
            'auth' => Auth::user(),
            'dataStatus' => $model->arrStatus,
            'dataManager' => $dataManager,
            'dataManagerAccount' => $dataManagerAccount,
        ]);
    }


    public function ajaxGetMissing(Request $request)
    {

        $columns     = ['cm.id', 'cm.ef_id', 'o.campaign_name', 'cm.name', 'cm.ef_status', 'cm.status', 'cm.created_at'];
        $columnsLike = ['cm.id', 'cm.ef_id', 'o.campaign_name', 'cm.name', 'cm.ef_status', 'cm.status', DB::raw('DATE_FORMAT(cm.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('offer_creative_missing AS cm')
            ->select('cm.id', 'cm.ef_id', 'o.campaign_name', 'cm.name', 'cm.ef_status', 'cm.status', DB::raw('DATE_FORMAT(cm.created_at, "%b %e, %Y %T") AS created_at'), 'cm.offer_id', DB::raw('oc.id AS can_attach'))
            ->join('offer AS o', 'o.id', '=', 'cm.offer_id')
            ->leftJoin('offer_creative AS oc', function($join){
                $join->on('oc.offer_id', '=', 'cm.offer_id');
                $join->on('oc.ef_id', '=', DB::raw('0'));
            })
            ->groupBy('cm.id')
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
            $query->where('o.manager_id', $request->manager);
        }
        if($request->status){
            $query->where('cm.status', $request->status);
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
            "recordsTotal"    => $sqlDB->table('offer_creative_missing')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function viewMissing($id)
    {
        $data = modelOfferCreativeMis::where('id', $id)->first();
        if($data){

            return view('admin.request.creative.viewmissing', [
                'data' => $data,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Creative not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function ignoreMissing($id)
    {
        $data = modelOfferCreativeMis::where('id', $id)->whereIn('status', [1])->first();
        if($data){

            $data->status = 2;
            $data->save();

            return redirect()->route('admin.request.creative.missing')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Creative has been ignored.",
                'autohide' => 1,
            ]]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Creative not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function addMissing($id)
    {
        $data = modelOfferCreativeMis::where('id', $id)->whereIn('status', [1, 2])->first();
        if($data){

            $dataIteration = modelOfferCreative::where('offer_id', $data->offer_id)->orderBy('iteration', 'DESC')->first();
            if($dataIteration){
                $iteration = $dataIteration->iteration + 1;
            } else {
                $iteration = 1;
            }

            $creative = new modelOfferCreative();
            $creative->fill([
                'offer_id' => $data->offer_id,
                'iteration' => $iteration,
                'name' => $data->name,
                'link' => $data->link,
                'price_in' => null,
                'price_out' => null,
                'lt_id' => 0,
                'ef_id' => $data->ef_id,
                'ef_status' => $data->ef_status,
                'status' => 3,
                'updated_at' => $data->updated_at ? : null,
                'created_at' => $data->created_at,
            ]);

            $data->status = 3;

            DB::beginTransaction();

            try {

                $creative->save();
                $data->save();

                DB::commit();

            } catch (PDOException $e) {

                DB::rollBack();

                return redirect()->back()->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'autohide' => 0,
                ]]);
            }

            return redirect()->route('admin.request.creative.missing')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Creative has been ignored.",
                'autohide' => 1,
            ]]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Creative not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function attachMissing($id)
    {
        $data = modelOfferCreativeMis::where('id', $id)->first();
        if($data){

            $modelNetwork = new modelNetwork();
            $dataNetwork = $modelNetwork->getNetwork();

            $dataCreative = $data->creative;
            if($dataCreative->name){
                $labelCreative = "(" . $dataCreative->{"ef_id"} . ") " .  $dataCreative->name;
            } else {
                $labelCreative = "";
            }

            return view('admin.request.creative.attachmissing', [
                'data' => $data,
                'labelCreative' => $labelCreative,
                'dataNetwork' => $dataNetwork,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Creative not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function saveAttachMissing(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'network_id' => 'required|integer',
            'ef_id' => 'required|integer',
            'creative_id' => [
                'required',
                'integer',
                Rule::unique('offer_creative_missing')->ignore($request->id, 'id'),
            ]
        ]);

        $dataAuth = Auth::user();

        $dataMissing = modelOfferCreativeMis::where('id', $request->id)->whereIn('status', [1, 2, 4])->first();
        if($dataMissing){

            if($dataMissing->creative_id){
                $dataExist = modelOfferCreative::where('id', $dataMissing->creative_id)->first();
                if($dataExist){
                    $dataExist->ef_id = 0;
                    $dataExist->status = 1;
                }
            }

            $dataCreative = modelOfferCreative::where('id', $request->creative_id)->first();
            if($dataCreative){

                $labelCreative = "(" . $dataCreative->{"ef_id"} . ") " .  $dataCreative->name;

                $dataCreative->ef_id = $dataMissing->ef_id;
                $dataCreative->status = 3;

                $dataMissing->status = 4;
                $dataMissing->creative_id = $dataCreative->id;
                $dataMissing->updated_by = $dataAuth->email;
                $dataMissing->updated_by_id = $dataAuth->id;

                DB::beginTransaction();

                try {

                    if(isset($dataExist)){
                        $dataExist->save();
                    }

                    $dataMissing->save();
                    $dataCreative->save();

                    DB::commit();

                } catch (PDOException $e) {

                    DB::rollBack();

                    return view('admin.request.creative.attachmissing', [
                        'status' => 'error',
                        'data' => $dataMissing,
                        'labelCreative' => $labelCreative,
                        'alert' => [
                            'type' => 'danger',
                            'title' => 'Error!',
                            'message' => $e->getMessage(),
                            'autohide' => 0,
                        ]
                    ]);
                }

                return view('admin.request.creative.attachmissing', [
                    'status' => 'success',
                    'data' => $dataMissing,
                    'labelCreative' => $labelCreative,
                    'alert' => [
                        'type' => 'success',
                        'title' => 'Success!',
                        'message' => "Creative has been attached !",
                        'autohide' => 1,
                    ]
                ]);

            } else {
                return view('admin.request.creative.attachmissing', [
                    'status' => 'error',
                    'data' => $dataMissing,
                    'labelCreative' => '',
                    'alert' => [
                        'type' => 'danger',
                        'title' => 'Error!',
                        'message' => "Creative not fount, please try again !",
                        'autohide' => 0,
                    ]
                ]);
            }

        } else {
            return view('admin.request.creative.attachmissing', [
                'status' => 'error',
                'alert' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => "Creative (missing) not fount, please try again !",
                    'autohide' => 0,
                ]
            ]);
        }

    }

}
