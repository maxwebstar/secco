<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PDOException;
use Exception;
use DB;

use App\Services\PipeDrive\Organization as PP_Organization;
use App\Services\PipeDrive\Person as PP_Person;

use App\Models\PipeDrive\Deal as modelDeal;
use App\Models\Currency as modelCurrency;
use App\Models\Country as modelCountry;
use App\Models\User as modelUser;
use App\Models\Network as modelNetwork;

class PipeDriveController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:pipedrive_access'], ['only' => ['index', 'ajaxGet']]);
        $this->middleware(['permission:pipedrive_change_status'], ['only' => ['delete']]);
    }

    function listenApiDeal(Request $request)
    {
//        $data = modelDeal::find(6);
//        $param = json_decode($data->request_body);

        $param = json_decode($request->getContent());

        if($param->current->status != "won"){
            return response()->json(['success' => 'Deal not won'], 200);
        }

        $pipePerson = new PP_Person();
        $pipeOrganization = new PP_Organization();

        $data = modelDeal::where('pd_deal_id', $param->current->id)->first();
        if($data){
            switch($data->status){
                case 0 :
                    return response()->json(['success' => 'Deal is deleted'], 200);
                    break;
                case 3 :
                    return response()->json(['success' => 'Deal is already added'], 200);
                    break;
            }
        } else {
            $data = new modelDeal();
        }

        $data->request_body = $request->getContent();
        $data->pd_deal_id = $param->current->id;
        $data->io_campaign_name = $param->current->title;
        $data->status = 1;

        if($param->current->currency){
            $currency = modelCurrency::where('key', $param->current->currency)->first();
            $data->currency_id = $currency ? $currency->id : 0;
        }
        if($param->current->user_id){
            $data->pd_user_id = $param->current->user_id;
            $user = modelUser::where('pipedrive_id', $param->current->user_id)->first();
            $data->manager_id = $user ? $user->id : 0;
        }

        if($param->current->org_id){
            $data->pd_organization_id = $param->current->org_id;
            $dataOrg = $pipeOrganization->getByID($param->current->org_id);
            if($dataOrg){
                $data->advertiser_name = $dataOrg['data']['name'];
                $data->advertiser_street1 = $dataOrg['data']['address_formatted_address'];
                $data->advertiser_zip = $dataOrg['data']['address_postal_code'];

                if($dataOrg['data']['address_country']) {
                    $country = modelCountry::where('name', $dataOrg['data']['address_country'])->first();
                    $data->advertiser_country = $country ? $country->key : null;
                }
            }
        }
        if($param->current->person_id){
            $data->pd_person_id = $param->current->person_id;
            $dataPerson = $pipePerson->getByID($param->current->person_id);
            if($dataPerson){
                $data->advertiser_contact = $dataPerson['data']['name'];

                if(isset($dataPerson['data']['email']) && is_array($dataPerson['data']['email'])){
                    $valueEmail = "";
                    foreach($dataPerson['data']['email'] as $iter){
                        $valueEmail .= $iter['value'] . ", ";
                    }
                    $data->advertiser_email = $valueEmail ? substr($valueEmail, 0, -2) : null;
                }
                if(isset($dataPerson['data']['phone']) && is_array($dataPerson['data']['phone'])){
                    $valuePhone = "";
                    foreach($dataPerson['data']['phone'] as $iter){
                        $valuePhone .= $iter['value'] . ", ";
                    }
                    $data->advertiser_phone = $valuePhone ? substr($valuePhone, 0, -2) : null;
                }
            }
        }

        try {

            $data->save();

        } catch(PDOException $e) {
            return response()->json(['error' => $e->getMessage()], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 200);
        }

        return response()->json(['success' => 'ok'], 200);

    }


    public function index()
    {
        $modelDeal = new modelDeal();
        $modelUser = new modelUser();
        $modelCurrency = new modelCurrency();
        $modelNetwork = new modelNetwork();

        $dataManager = $modelUser->getManager();
        $dataNetwork = $modelNetwork->getNetwork();

        return view('admin.pipedrive.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
            'dataStatus' => $modelDeal->arrStatus,
            'dataCurrency' => $modelCurrency->getAllCurrencyKeyID(),
            'dataNetwork' => $dataNetwork,
        ]);
    }


    public function ajaxGet(Request $request)
    {
        $columns     = ['pd.id', 'pd.pd_deal_id', 'pd.advertiser_name', 'pd.io_campaign_name', 'u.name', 'pd.status', 'pd.created_at'];
        $columnsLike = ['pd.id', 'pd.pd_deal_id', 'pd.advertiser_name', 'pd.io_campaign_name', 'u.name', 'pd.status',  DB::raw('DATE_FORMAT(pd.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value

        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('pipe_drive_deal as pd')
            ->select('pd.id', 'pd.pd_deal_id', 'pd.advertiser_name', 'pd.io_campaign_name', 'u.name AS manager', 'pd.status', DB::raw('DATE_FORMAT(pd.created_at, "%b %e, %Y %T") AS created_at'), 'a.id AS advertiser_id',
                'pd.pd_organization_id',
                'pd.pd_person_id',
                'pd.pd_user_id',
                'pd.currency_id',
                'pd.advertiser_contact',
                'pd.advertiser_country',
                'pd.advertiser_street1',
                'pd.advertiser_zip',
                'pd.advertiser_email',
                'pd.advertiser_phone',
                DB::raw('IF(pd.updated_at, DATE_FORMAT(pd.updated_at, "%b %e, %Y %T"), "") AS updated_at')
            )
            ->leftJoin('advertiser as a', 'a.name', '=', 'pd.advertiser_name')
            ->leftJoin('users as u', function($join){
                $join->on('u.pipedrive_id', '>', DB::raw('0'));
                $join->on('u.pipedrive_id', '=', 'pd.pd_user_id');
            })
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
            $query->where('u.pipedrive_id', $request->manager);
        }
        if(isset($request->status)){
            $query->where('pd.status', $request->status);
            if($request->status != 0){
                $query->where('pd.status', '!=', 0);
            }
        }
        if($request->without_advertiser){
            $query->whereNull('a.id');
        }

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('pipe_drive_deal')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function delete($id)
    {
        $data = modelDeal::find($id);
        if($data){

            $data->status = 0;
            $data->save();

            return redirect()->route('admin.pipedrive.index')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Deal $data->io_campaign_name has been updated",
                'autohide' => 1,
            ]]);

        } else {

            return redirect()->route('admin.pipedrive.index')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Deal has not been updated !",
                'autohide' => 0,
            ]]);

        }
    }


}
