<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Offer as modelOffer;
use App\Models\OfferReport as modelOfferReport;
use App\Models\User as modelUser;
use App\Models\OfferCategory as modelCategory;
use App\Models\CampaignType as modelCampaignType;
use App\Models\CapType as modelCapType;
use App\Models\CapUnit as modelCapUnit;
use App\Models\Domain as modelDomain;
use App\Models\Pixel as modelPixel;
use App\Models\Network as modelNetwork;
use App\Models\OfferCreative as modelCreative;
use App\Models\Advertiser as modelAdvertiser;
use App\Models\EmailTemplate as modelEmailTemplate;
use App\Models\Currency as modelCurrency;

use App\Services\Mailer;
use App\Services\LinkTrust\Offer as LT_Offer;
use App\Services\EverFlow\Offer as EF_Offer;

use DB;
use Exception;
use PDOException;


class OfferController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:change_offer_new_offer'], ['only' => ['add', 'saveAdd']]);
        $this->middleware(['permission:change_offer_access'], ['only' => ['index', 'ajaxGet', 'view']]);
        $this->middleware(['permission:change_offer_edit_offer'], ['only' => ['editNew', 'saveEditNew']]);
        $this->middleware(['permission:change_offer_change_status'], ['only' => ['decline', 'saveDecline', 'approve']]);
    }


    public function index()
    {
        $model = new modelOffer();
        $modelUser = new modelUser();

        $dataManager = $modelUser->getManager();

        return view('admin.offer.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
            'dataStatus' => $model->arrStatus,
        ]);
    }


    public function ajaxGet(Request $request)
    {

        $columns     = ['o.id', 'a.name', 'o.campaign_name', 'o.status', 'u.name', 'o.created_at'];
        $columnsLike = ['o.id', 'a.name', 'o.campaign_name', 'o.status', 'u.name', DB::raw('DATE_FORMAT(o.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value

        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('offer AS o')
            ->select('o.id', 'a.name AS advertiser_name', 'o.campaign_name', 'o.status', 'u.name AS created_name', DB::raw('DATE_FORMAT(o.created_at, "%b %e, %Y %T") AS created_at'))
            ->join('advertiser AS a', 'a.id', '=', 'o.advertiser_id')
            ->leftJoin('users AS u', 'u.id', '=', 'o.created_by_id')
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
            $query->where('o.created_by_id', $request->created_by);
        }
        if($request->status){
            $query->where('o.status', $request->status);
        }

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('offer')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function add($network = null)
    {
        $model = new modelOffer();
        $modelUser = new modelUser();
        $modelCategory = new modelCategory();
        $modelCampaignType = new modelCampaignType();
        $modelCapType = new modelCapType();
        $modelCapUnit = new modelCapUnit();
        $modelDomain = new modelDomain();
        $modelPixel = new modelPixel();
        $modelNetwork = new modelNetwork();

        $dataManager = $modelUser->getManager();
        $dataManagerAccount = $modelUser->getManagerAccount();
        $dataCategory = $modelCategory->getByNetwork($network);
        $dataCampaignType = $modelCampaignType->getByNetwork($network);
        $dataCapType = $modelCapType->getType();
        $dataCapUnit = $modelCapUnit->getUnit();
        $dataDomain = $modelDomain->getByNetwork($network);
        $dataPixel = $modelPixel->getByNetwork($network);
        $dataNetwork = $modelNetwork->getNetwork();

        $dataCurrency = [];
        $tmpCurrency = modelCurrency::orderBy('position', 'ASC')->get();

        foreach($tmpCurrency as $iter){
            $dataCurrency[$iter->id] = $iter;
        }

        return view('admin.offer.add', [
            'auth' => Auth::user(),
            'data' => $model,
            'dataNetwork' => $dataNetwork,
            'dataManager' => $dataManager,
            'dataManagerAccount' => $dataManagerAccount,
            'dataCategory' => $dataCategory,
            'dataCampaignType' => $dataCampaignType,
            'dataCapType' => $dataCapType,
            'dataCapUnit' => $dataCapUnit,
            'dataDomain' => $dataDomain,
            'dataPixel' => $dataPixel,
            'dataCurrency' => $dataCurrency,
            'network' => $network,
        ]);
    }


    public function editNew($id, $network = null)
    {
        $model = new modelOffer();
        $modelUser = new modelUser();
        $modelCategory = new modelCategory();
        $modelCampaignType = new modelCampaignType();
        $modelCapType = new modelCapType();
        $modelCapUnit = new modelCapUnit();
        $modelDomain = new modelDomain();
        $modelPixel = new modelPixel();
        $modelNetwork = new modelNetwork();

        $data = $model->where('id', $id)->whereIn('status', [1,2])->first();
        if(!$data){

            return redirect()->route('admin.offer.add')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Offer not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $dataGeos = $data->getGeos();
        $dataAdvetiser = modelAdvertiser::where('id', $data->advertiser_id)->first();
        $dataCreative = modelCreative::where('offer_id', $data->id)->whereIn('status', [1,2])->get();

        $geos_key = [];
        $labelGeos = "";
        $labelAdvertiser = "";

        $creative_id = [];
        $creative_name = [];
        $creative_link = [];
        $creative_price_in = [];
        $creative_price_out = [];

        if($dataGeos) {
            foreach ($dataGeos as $iter) {
                $geos_key[] = $iter->key;
                $labelGeos .= $iter->name . ',';
            }
            $labelGeos = substr($labelGeos, 0, -1);
        }
        if($dataAdvetiser){
            $labelAdvertiser = $dataAdvetiser->name;
        }
        if($dataCreative){
            foreach($dataCreative as $iter){
                $creative_id[] = $iter->id;
                $creative_name[] = $iter->name;
                $creative_link[] = $iter->link;
                $creative_price_in[] = $iter->price_in;
                $creative_price_out[] = $iter->price_out;
            }
        }

        $dataCurrency = [];
        $tmpCurrency = modelCurrency::orderBy('position', 'ASC')->get();

        foreach($tmpCurrency as $iter){
            $dataCurrency[$iter->id] = $iter;
        }

        if($data->need_api_lt){
            $network = "lt";
        }
        if($data->need_api_ef){
            $network = "ef";
        }
        switch($network){
            case 'lt' : $data->need_api_lt = 1; break;
            case 'ef' : $data->need_api_ef = 1; break;
            default : break;
        }

        $dataManager = $modelUser->getManager();
        $dataManagerAccount = $modelUser->getManagerAccount();
        $dataCategory = $modelCategory->getByNetwork($network);
        $dataCampaignType = $modelCampaignType->getByNetwork($network);
        $dataCapType = $modelCapType->getType();
        $dataCapUnit = $modelCapUnit->getUnit();
        $dataDomain = $modelDomain->getByNetwork($network);
        $dataPixel = $modelPixel->getByNetwork($network);
        $dataNetwork = $modelNetwork->getNetwork();

        return view('admin.offer.edit.new', [
            'auth' => Auth::user(),
            'data' => $data,
            'dataNetwork' => $dataNetwork,
            'dataManager' => $dataManager,
            'dataManagerAccount' => $dataManagerAccount,
            'dataCategory' => $dataCategory,
            'dataCampaignType' => $dataCampaignType,
            'dataCapType' => $dataCapType,
            'dataCapUnit' => $dataCapUnit,
            'dataDomain' => $dataDomain,
            'dataPixel' => $dataPixel,
            'dataCurrency' => $dataCurrency,
            'geos_key' => $geos_key,
            'labelGeos' => $labelGeos,
            'labelAdvertiser' => $labelAdvertiser,
            'creative_id' => $creative_id,
            'creative_name' => $creative_name,
            'creative_link' => $creative_link,
            'creative_price_in' => $creative_price_in,
            'creative_price_out' => $creative_price_out,
            'network' => $network
        ]);
    }


    public function edit($id)
    {
        $model = new modelOffer();
        $modelUser = new modelUser();
        $modelCategory = new modelCategory();
        $modelCampaignType = new modelCampaignType();
        $modelDomain = new modelDomain();
        $modelPixel = new modelPixel();
        $modelNetwork = new modelNetwork();

        $data = $model->where('id', $id)->whereIn('status', [3])->first();
        if(!$data){

            return redirect()->route('admin.offer.add')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Offer not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        if($data->need_api_lt || $data->lt_id){
            $network = "lt";
        }
        if($data->need_api_ef || $data->ef_id){
            $network = "ef";
        }
        switch($network){
            case 'lt' : $data->need_api_lt = 1; break;
            case 'ef' : $data->need_api_ef = 1; break;
            default : break;
        }

        $dataManager = $modelUser->getManager();
        $dataManagerAccount = $modelUser->getManagerAccount();
        $dataCategory = $modelCategory->getByNetwork($network);
        $dataDomain = $modelDomain->getByNetwork($network);
        $dataPixel = $modelPixel->getByNetwork($network);
        $dataNetwork = $modelNetwork->getNetwork();
        $dataCampaignType = $modelCampaignType->getByNetwork($network);

        return view('admin.offer.edit', [
            'auth' => Auth::user(),
            'data' => $data,
            'dataNetwork' => $dataNetwork,
            'dataManager' => $dataManager,
            'dataManagerAccount' => $dataManagerAccount,
            'dataCategory' => $dataCategory,
            'dataCampaignType' => $dataCampaignType,
            'dataDomain' => $dataDomain,
            'dataPixel' => $dataPixel,
            'network' => $network
        ]);

    }


    public function saveAdd(Request $request)
    {
        $this->validate($request, [
            'campaign_name' => 'required|max:255',
            'campaign_type' => 'nullable|max:7',
            'campaign_link' => 'required|url',
            'manager_id' => 'required|integer',
            'manager_account_id' => 'required|integer',
            'advertiser_id' => 'required|integer',
            'advertiser_contact' => 'required|max:255',
            'category_id' => 'nullable|integer',
            'domain_id' => 'required|integer',
            'pixel_id' => 'nullable|integer',
            'redirect' => 'required|integer',
            'redirect_url' => 'nullable|url',

            'cap_type_id' => 'nullable|integer',
            'cap_unit_id' => 'nullable|integer',
            'cap_monetary' => 'required_if:cap_unit_id,1|nullable',
            'price_in' => 'required',
            'price_out' => 'required',

            'geos' => 'nullable|array',
            'geo_redirect_url' => 'nullable|url|max:255',

            'accepted_traffic' => 'nullable',
            'affiliate_note' => 'nullable',
            'internal_note' => 'nullable',

            'creative_name' => 'nullable|array',
            'creative_link' => 'nullable|array',
            'creative_price_in' => 'nullable|array',
            'creative_price_out' => 'nullable|array',

            'tracking_platform' => 'required_without_all:linktrust,everflow',
            'ef_status' => 'required_with:everflow|max:31',
        ]);

        $auth = Auth::user();
        $dataAdvertiser = modelAdvertiser::findOrFail($request->advertiser_id);

        $arrGeos = $request->geos;
        $strGeos = "";
        if($arrGeos && is_array($arrGeos)){
            $strGeos = implode(",", $arrGeos);
        }

        $data = new modelOffer();
        $data->fill([
            'campaign_name' => $request->campaign_name,
            'campaign_type' => $request->campaign_type,
            'campaign_link' => $request->campaign_link,
            'manager_id' => $request->manager_id,
            'manager_account_id' => $request->manager_account_id ? : 0,
            'advertiser_id' => $request->advertiser_id,
            'advertiser_contact' => $request->advertiser_contact,
            'advertiser_email' => $dataAdvertiser->email,
            'offer_category_id' => $request->category_id,
            'domain_id' => $request->domain_id,
            'pixel_id' => $request->pixel_id,
            'pixel_location' => $request->pixel_location,
            'redirect' => $request->redirect,
            'redirect_url' => $request->redirect_url,

            'cap_type_id' => $request->cap_type_id,
            'cap_unit_id' => $request->cap_unit_id,
            'cap_monetary' => $request->cap_monetary,
            'cap_lead' => $request->cap_lead,
            'price_in' => $request->price_in,
            'price_out' => $request->price_out,

            'geos' => $strGeos,
            'geo_redirect_url' => $request->geo_redirect_url,

            'accepted_traffic' => $request->accepted_traffic,
            'affiliate_note' => $request->affiliate_note,
            'internal_note' => $request->internal_note,

            'status' => 1,
            'created_by' => $auth->email,
            'created_by_id' => $auth->id,

            'need_api_lt' => $request->linktrust ? 1 : 0,
            'need_api_ef'=> $request->everflow ? 1 : 0,
            'ef_status' => $request->ef_status,
        ]);

        DB::beginTransaction();

        try {

            $data->save();

            if($request->creative_name && is_array($request->creative_name)){
                foreach($request->creative_name as $key => $creativeName){

                    $creativeLink = $request->creative_link[$key];
                    $creativePriceIn = $request->creative_price_in[$key];
                    $creativePriceOut = $request->creative_price_out[$key];

                    if($creativeName && $creativeLink){

                        $creative = new modelCreative();
                        $creative->fill([
                            'offer_id' => $data->id,
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

        $dataCreative = modelCreative::where('offer_id', $data->id)->get();

        $mailer = new Mailer();
        $mailer->sendNewOffer($data, $dataCreative);

        return redirect()->route('admin.offer.add')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Offer $data->campaign_name has been created !",
            'autohide' => 1,
        ]]);
    }


    public function saveEditNew(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'campaign_name' => 'required|max:255',
            'campaign_type' => 'nullable|max:7',
            'campaign_link' => 'required|url',
            'manager_id' => 'required|integer',
            'manager_account_id' => 'required|integer',
            'advertiser_id' => 'required|integer',
            'advertiser_contact' => 'required|max:255',
            'category_id' => 'nullable|integer',
            'domain_id' => 'required|integer',
            'pixel_id' => 'nullable|integer',
            'redirect' => 'required|integer',
            'redirect_url' => 'nullable|url',

            'cap_type_id' => 'nullable|integer',
            'cap_unit_id' => 'nullable|integer',
            'cap_monetary' => 'required_if:cap_unit_id,1|nullable',
            'price_in' => 'required',
            'price_out' => 'required',

            'geos' => 'nullable|array',
            'geo_redirect_url' => 'nullable|url|max:255',

            'accepted_traffic' => 'nullable',
            'affiliate_note' => 'nullable',
            'internal_note' => 'nullable',

            'creative_id' => 'nullable|array',
            'creative_name' => 'nullable|array',
            'creative_link' => 'nullable|array',
            'creative_price_in' => 'nullable|array',
            'creative_price_out' => 'nullable|array',

            'tracking_platform' => 'required_without_all:linktrust,everflow',
            'ef_status' => 'required_with:everflow|max:31',
        ]);

        $dataAdvertiser = modelAdvertiser::findOrFail($request->advertiser_id);

        $arrGeos = $request->geos;
        $strGeos = "";
        if($arrGeos && is_array($arrGeos)){
            $strGeos = implode(",", $arrGeos);
        }

        $data = modelOffer::where('id', $request->id)->whereIn('status', [1,2])->first();
        if(!$data){
            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Offer not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $dataOld = $data;
        $dataCreativeOld = modelCreative::where('offer_id', $data->id)->get();

        $data->fill([
            'campaign_name' => $request->campaign_name,
            'campaign_type' => $request->campaign_type,
            'campaign_link' => $request->campaign_link,
            'manager_id' => $request->manager_id,
            'manager_account_id' => $request->manager_account_id ? : 0,
            'advertiser_id' => $request->advertiser_id,
            'advertiser_contact' => $request->advertiser_contact,
            'advertiser_email' => $dataAdvertiser->email,
            'offer_category_id' => $request->category_id,
            'domain_id' => $request->domain_id,
            'pixel_id' => $request->pixel_id,
            'pixel_location' => $request->pixel_location,
            'redirect' => $request->redirect,
            'redirect_url' => $request->redirect_url,

            'cap_type_id' => $request->cap_type_id,
            'cap_unit_id' => $request->cap_unit_id,
            'cap_monetary' => $request->cap_monetary,
            'cap_lead' => $request->cap_lead,
            'price_in' => $request->price_in,
            'price_out' => $request->price_out,

            'geos' => $strGeos,
            'geo_redirect_url' => $request->geo_redirect_url,

            'accepted_traffic' => $request->accepted_traffic,
            'affiliate_note' => $request->affiliate_note,
            'internal_note' => $request->internal_note,

            'need_api_lt' => $request->linktrust ? 1 : 0,
            'need_api_ef'=> $request->everflow ? 1 : 0,
            'ef_status' => $request->ef_status,
        ]);

        DB::beginTransaction();

        try {

            $data->save();

            if($request->creative_name && is_array($request->creative_name)){
                foreach($request->creative_name as $key => $creativeName){

                    $creativeId = $request->creative_id[$key];
                    $creativeLink = $request->creative_link[$key];
                    $creativePriceIn = $request->creative_price_in[$key];
                    $creativePriceOut = $request->creative_price_out[$key];

                    if($creativeName && $creativeLink){

                        if($creativeId){
                            $creative = modelCreative::where('id', $creativeId)
                                ->where('offer_id', $data->id)
                                ->whereIn('status', [1,2])
                                ->first();
                            if(!$creative){
                                continue;
                            }
                        } else {
                            $creative = new modelCreative();
                        }
                        $creative->fill([
                            'offer_id' => $data->id,
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

        if($data->status == 2) {
            $mailer = new Mailer();
            $mailer->sendUpdateDeclineOffer($data, $dataOld, $dataCreativeOld);
        }

        return redirect()->route('admin.offer.index')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Offer $data->campaign_name has been updated !",
            'autohide' => 1,
        ]]);
    }


    public function saveEdit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            /*'campaign_name' => 'required|max:255',
            'campaign_type' => 'nullable|max:7',
            'campaign_link' => 'required|url',*/
            'manager_id' => 'required|integer',
            'manager_account_id' => 'required|integer',
            'category_id' => 'nullable|integer',
            'domain_id' => 'required|integer',
            'pixel_id' => 'nullable|integer',

            /*'geos' => 'nullable|array',
            'geo_redirect_url' => 'nullable|url|max:255',*/

            'accepted_traffic' => 'nullable',
            'affiliate_note' => 'nullable',
            'internal_note' => 'nullable',

            'tracking_platform' => 'required_without_all:linktrust,everflow',
            'ef_status' => 'required_with:everflow|max:31',
        ]);

        $data = modelOffer::where('id', $request->id)->whereIn('status', [3])->first();
        if(!$data){
            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Offer not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        $data->fill([
            /*'campaign_name' => $request->campaign_name,
            'campaign_type' => $request->campaign_type,
            'campaign_link' => $request->campaign_link,*/
            'manager_id' => $request->manager_id,
            'manager_account_id' => $request->manager_account_id ? : 0,
            'offer_category_id' => $request->category_id,
            'domain_id' => $request->domain_id,
            'pixel_id' => $request->pixel_id,

            /*'geos' => $strGeos,
            'geo_redirect_url' => $request->geo_redirect_url,*/

            'accepted_traffic' => $request->accepted_traffic,
            'affiliate_note' => $request->affiliate_note,
            'internal_note' => $request->internal_note,

            /*'need_api_lt' => $request->linktrust ? 1 : 0,*/
            'need_api_ef'=> $request->everflow ? 1 : 0,
            'ef_status' => $request->ef_status,
        ]);

        //DB::beginTransaction();
        try {

            if($data->ef_id){
                $ef_Offer = new EF_Offer();
                $ef_resp = $ef_Offer->updateLiteOffer($data);

                if($ef_resp['updated']){
                    $data->save();
                } else {
                    return redirect()->route('admin.offer.index')->with(['message' => [
                        'type' => 'danger',
                        'title' => 'Error! EverFlow Api',
                        'message' => $ef_resp['message'],
                        'autohide' => 0,
                    ]]);
                }
            }
            //DB::commit();

        } catch (PDOException $e) {

            //DB::rollBack();
            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'autohide' => 0,
            ]]);

        } catch (Exception $e) {

            //DB::rollBack();
            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'autohide' => 0,
            ]]);
        }

        return redirect()->route('admin.offer.index')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Offer $data->campaign_name has been updated !",
            'autohide' => 1,
        ]]);
    }


    public function view($id)
    {
        $data = modelOffer::where('id', $id)->first();
        if($data){

            $dataCreative = modelCreative::where('offer_id', $data->id)->get();

            return view('admin.offer.view', [
                'data' => $data,
                'dataCreative' => $dataCreative,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Offer not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function decline($id)
    {
        $data = modelOffer::where('id', $id)->whereIn('status', [1])->first();
        if($data){

            $dataTemplate = modelEmailTemplate::where('name', 'advertiser_offer_new_offer_declined')->where('status', 3)->first();
            if(!$dataTemplate){
                throw new Exception('Error: Email Template not found !');
            }

            return view('admin.offer.decline', [
                'data' => $data,
                'dataTemplate' => $dataTemplate,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Offer not fount, please try again !",
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

        $data = modelOffer::where('id', $request->id)->whereIn('status', [1])->first();
        if($data){

            DB::beginTransaction();

            try {

                $data->status = 2;
                $data->save();

                /*modelCreative::where('status', 1)
                    ->where('offer_id', $data->id)
                    ->update(['status' => 2]);*/

                DB::commit();

            } catch (Exception $e) {

                DB::rollBack();

                return redirect()->back()->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'autohide' => 0,
                ]]);

            } catch (PDOException $e) {

                DB::rollBack();

                return redirect()->back()->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'autohide' => 0,
                ]]);
            }

            $mailer = new Mailer();
            $mailer->sendDeclineOffer($data, $request->reason);

            $alert = [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Offer $data->campaign_name has been declined.",
                'hide' => 1,
            ];

            return view('admin.offer.decline', ['status' => 'success', 'alert' => $alert, 'data' => $data]);

        } else {

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Offer not fount, please try again !",
                'hide' => 0,
            ];

            return view('admin.offer.decline', ['status' => 'error', 'alert' => $alert, 'data' => $data]);
        }
    }


    public function approve($id)
    {
        $data = modelOffer::where('id', $id)->whereIn('status', [1, 2])->first();
        if($data){

            $offer_network_str = "";
            if($data->need_api_lt){
                $lt_Offer = new LT_Offer();
                $lt_resp = $lt_Offer->createOffer($data);
                if($lt_resp['lt_id']){
                    $data->lt_id = $lt_resp['lt_id'];
                    $offer_network_str .= " (LinkTrust id: $data->lt_id) ";
                } else {
                    return redirect()->route('admin.offer.index')->with(['message' => [
                        'type' => 'danger',
                        'title' => 'Error! LinkTrust Api',
                        'message' => $lt_resp['message'],
                        'autohide' => 0,
                    ]]);
                }
            }
            if($data->need_api_ef){
                $ef_Offer = new EF_Offer();
                $ef_resp = $ef_Offer->createOffer($data);

                if($ef_resp['ef_id']){
                    $data->ef_id = $ef_resp['ef_id'];
                    $offer_network_str .= " (EverFlow id: $data->ef_id) ";
                } else {
                    return redirect()->route('admin.offer.index')->with(['message' => [
                        'type' => 'danger',
                        'title' => 'Error! LinkTrust Api',
                        'message' => $ef_resp['message'],
                        'autohide' => 0,
                    ]]);
                }
            }

            $data->status = 3;
            $data->save();

            return redirect()->route('admin.offer.index')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Offer $data->campaign_name has been approve $offer_network_str.",
                'autohide' => 0,
            ]]);


        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Offer not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function profile($id = 0)
    {
        $modelNetwork = new modelNetwork();
        $modelOfferReport = new modelOfferReport();

        $data = modelOffer::where('id', $id)->first();
        if($data){
            $dataReport = $modelOfferReport->getData($data->id);
            $dataDate['start'] = $modelOfferReport->where('offer_id', $data->id)->orderBy('date', 'ASC')->first();
            $dataDate['end'] = $modelOfferReport->where('offer_id', $data->id)->orderBy('date', 'DESC')->first();
        } else {
            $dataReport = [];
            $dataDate['start'] = "";
            $dataDate['end'] = "";
        }

        return view('admin.offer.profile', [
            'count' => modelOffer::count(),
            'data' => $data,
            'dataNetwork' => $modelNetwork->getNetwork(),
            'dataReport' => $dataReport,
            'dataDate' => $dataDate,
        ]);
    }

}
