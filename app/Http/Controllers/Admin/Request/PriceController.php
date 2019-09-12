<?php

namespace App\Http\Controllers\Admin\Request;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Request\Price as modelRequestPrice;
use App\Models\Network as modelNetwork;
use App\Models\User as modelUser;
use App\Models\EmailTemplate as modelEmailTemplate;

use DB;
use App\Services\Mailer;
use App\Services\EverFlow\Offer as EF_Offer;

class PriceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:request_price_request'], ['only' => ['index', 'ajax-get', 'add', 'save-add', 'edit', 'save-edit']]);
        $this->middleware(['permission:todo_price_change'], ['only' => ['index', 'ajax-get', 'edit', 'save-edit', 'view', 'decline', 'save-decline', 'approve']]);
    }


    public function index()
    {
        $model = new modelRequestPrice();
        $modelUser = new modelUser();

        $dataManager = $modelUser->getManager();

        return view('admin.request.price.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
            'dataStatus' => $model->arrStatus,
        ]);
    }


    public function ajaxGet(Request $request)
    {
        $columns     = ['rp.id', 'n.short_name', 'o.campaign_name', 'a.name', 'rp.current_price_in', 'rp.price_in', 'rp.date', 'rp.status', 'u.name', 'rp.created_at'];
        $columnsLike = ['rp.id', 'n.short_name', 'o.campaign_name', 'a.name', 'rp.current_price_in', 'rp.price_in', DB::raw('DATE_FORMAT(rp.date, "%b %e, %Y")'), 'rp.status', DB::raw('IF(rp.created_by_id > 0, u.name, rp.created_by)'), DB::raw('DATE_FORMAT(rp.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value

        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('request_price AS rp')
            ->select('rp.id', 'n.short_name', 'o.campaign_name', DB::raw('a.name as affiliate_name'), 'rp.current_price_in', 'rp.current_price_out', 'rp.price_in', 'rp.price_out', DB::raw('DATE_FORMAT(rp.date, "%b %e, %Y") AS date'), 'rp.status', DB::raw('IF(rp.created_by_id > 0, u.name, rp.created_by) AS created_name'), DB::raw('DATE_FORMAT(rp.created_at, "%b %e, %Y %T") AS created_at'),
                'rp.affiliate_all',
                DB::raw('o.lt_id as offer_lt_id'),
                DB::raw('o.ef_id as offer_ef_id'),
                DB::raw('a.lt_id as affiliate_lt_id'),
                DB::raw('a.ef_id as affiliate_ef_id'))
            ->join('network AS n', 'n.id', '=', 'rp.network_id')
            ->join('offer AS o', 'o.id', '=', 'rp.offer_id')
            ->leftJoin('affiliate AS a', 'a.id', '=', 'rp.affiliate_id')
            ->leftJoin('users AS u', 'u.id', '=', 'rp.created_by_id')
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
            $query->where('rp.created_by_id', $request->created_by);
        }
        if($request->status){
            $query->where('rp.status', $request->status);
        }
        if($request->fx_rate){
            $query->where('rp.is_fx_rate', 1);
        } else {
            $query->where('rp.is_fx_rate', 0);
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
        $model = new modelRequestPrice();
        $modelNetwork = new modelNetwork();

        $dataNetwork = $modelNetwork->getNetwork();

        return view('admin.request.price.add', [
            'dataType' => $model->arrType,
            'dataNetwork' => $dataNetwork,
        ]);
    }


    public function edit($id)
    {
        $modelNetwork = new modelNetwork();

        $data = modelRequestPrice::where('id', $id)->whereIn('status', [1, 2])->first();
        if (!$data) {

            return redirect()->route('admin.request.cap.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Cap request not fount, please try again !",
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

        if (!$data->affiliate_all){
            $dataAffiliate = $data->affiliate;

            if (isset($dataOffer->{$network->field_name})) {
                $labelAffiliate = "(" . $dataOffer->{$network->field_name} . ") " . $dataAffiliate->name;
            } else {
                $labelAffiliate = $dataAffiliate->name;
            }
        } else {
            $labelAffiliate = "";
        }

        return view('admin.request.price.edit', [
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
            'offer_id' => 'required|integer',
            'affiliate_id' => 'nullable|required_without:affiliate_all',
            'affiliate_all' => 'nullable|integer',
            'date' => 'required|date',
            'price_in' => 'required|max:31',
            'price_out' => 'required|max:31',
            'current_price_in' => 'nullable|max:31',
            'current_price_out' => 'nullable|max:31',
            'type' => 'required|integer',
            'cap_change' => 'nullable|integer',
            'reason' => 'required',
        ]);

        $auth = Auth::user();

        $data = new modelRequestPrice();
        $data->fill([
            'network_id' => $request->network_id,
            'offer_id' => $request->offer_id,
            'affiliate_id' => $request->affiliate_id ? : 0,
            'affiliate_all' => $request->affiliate_all ? 1 : 0,
            'date' => date('Y-m-d', strtotime($request->date)),
            'price_in' => $request->price_in ? : null,
            'price_out' => $request->price_out ? : null,
            'current_price_in' => $request->current_price_in ? : null,
            'current_price_out' => $request->current_price_out ? : null,
            'type' => $request->type,
            'cap_change' => $request->cap_change ? 1 : 0,
            'reason' => $request->reason,
            'status' => 1,
            'created_by' => $auth->email,
            'created_by_id' => $auth->id,
        ]);

        $data->save();

        $mailer = new Mailer();
        $mailer->sendNewRequestPrice($data);

        return redirect()->route('admin.request.price.add')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "New Price Request has been created !",
            'autohide' => 1,
        ]]);
    }


    public function saveEdit(Request $request)
    {
        $this->validate($request, [
            'network_id' => 'required|integer',
            'offer_id' => 'required|integer',
            'affiliate_id' => 'nullable|required_without:affiliate_all',
            'affiliate_all' => 'nullable|integer',
            'date' => 'required|date',
            'price_in' => 'required|max:31',
            'price_out' => 'required|max:31',
            'current_price_in' => 'nullable|max:31',
            'current_price_out' => 'nullable|max:31',
            'type' => 'required|integer',
            'cap_change' => 'nullable|integer',
            'reason' => 'required',
        ]);

        $auth = Auth::user();

        $data = modelRequestPrice::where('id', $request->id)->first();
        if(!$data){

            return redirect()->route('admin.request.price.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Price request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $data->fill([
            'network_id' => $request->network_id,
            'offer_id' => $request->offer_id,
            'affiliate_id' => $request->affiliate_id ? : 0,
            'affiliate_all' => $request->affiliate_all ? 1 : 0,
            'date' => date('Y-m-d', strtotime($request->date)),
            'price_in' => $request->price_in ? : null,
            'price_out' => $request->price_out ? : null,
            'current_price_in' => $request->current_price_in ? : null,
            'current_price_out' => $request->current_price_out ? : null,
            'type' => $request->type,
            'cap_change' => $request->cap_change ? 1 : 0,
            'reason' => $request->reason,
        ]);

        $data->save();

        return redirect()->route('admin.request.price.index')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Price Request has been updated !",
            'autohide' => 1,
        ]]);
    }


    public function view($id)
    {
        $data = modelRequestPrice::where('id', $id)->first();
        if($data){

            return view('admin.request.price.view', [
                'data' => $data,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Price request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function decline($id)
    {
        $data = modelRequestPrice::where('id', $id)->whereIn('status', [1])->first();
        if($data){

            $dataTemplate = modelEmailTemplate::where('name', 'request_price_change_declined')->where('status', 3)->first();
            if(!$dataTemplate){
                throw new Exception('Error: Email Template not found !');
            }

            return view('admin.request.price.decline', [
                'data' => $data,
                'dataTemplate' => $dataTemplate,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Price request not fount, please try again !",
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

        $data = modelRequestPrice::where('id', $request->id)->whereIn('status', [1])->first();
        if($data){

            $data->status = 2;
            $data->save();

            $mailer = new Mailer();
            $mailer->sendDeclinePriceRequest($data, $request->reason);

            $alert = [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Price request has been declined.",
                'hide' => 1,
            ];

            return view('admin.request.price.decline', ['status' => 'success', 'alert' => $alert, 'data' => $data]);

        } else {

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Price request not fount, please try again !",
                'hide' => 0,
            ];

            return view('admin.request.price.decline', ['status' => 'error', 'alert' => $alert, 'data' => $data]);
        }
    }


    public function approve($id)
    {
        $data = modelRequestPrice::where('id', $id)->whereIn('status', [1, 2])->first();
        if($data){

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
            if($data->network_id == 2){
                $ef_Offer = new EF_Offer();
                $ef_resp = $ef_Offer->updateOfferPrice($data);

                if($ef_resp['ef_id']){
                    $data->ef_id = $ef_resp['ef_id'];
                    $offer_network_str .= " (EverFlow id: $data->ef_id) ";
                } else {
                    return redirect()->route('admin.request.price.index')->with(['message' => [
                        'type' => 'danger',
                        'title' => 'Error! EverFlow Api',
                        'message' => $ef_resp['message'],
                        'autohide' => 0,
                    ]]);
                }

            } else {

            }

            $data->status = 3;
            $data->save();

            return redirect()->route('admin.request.price.index')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Price request (id: $data->id) $offer_network_str has been approve",
                'autohide' => 0,
            ]]);


        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Price request not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }

}
