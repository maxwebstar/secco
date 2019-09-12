<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Exception;
use PDOException;
use DB;
use App\Models\Advertiser;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\Frequency;
use App\Models\Currency;
use App\Models\PipeDrive\Deal;
use App\Models\IO as modelIO;
use App\Models\Network as modelNetwork;
use App\Models\AdvertiserInitPayment as modelAdvertiserInitPayment;
use App\Models\AdvertiserMissing as modelAdvertiserMissing;

use App\Services\Validator\EmailString;

use App\Services\LinkTrust\Advertiser as LT_Advertiser;
use App\Services\EverFlow\Advertiser as EF_Advertiser;

class AdvertiserController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:advertiser_access'], ['only' => ['index', 'ajax-get']]);
        $this->middleware(['permission:advertiser_new_advertiser'], ['only' => ['add', 'save-add']]);
        $this->middleware(['permission:advertiser_view'], ['only' => ['edit']]);
        $this->middleware(['permission:advertiser_edit'], ['only' => ['save-edit']]);
        $this->middleware(['permission:advertiser_missing'], ['only' => ['missing', 'ajax-get-missing', 'view-missing', 'add-missing', 'save-add-missing']]);
    }


    public function index()
    {
        $modelUser = new User();

        $dataManager = $modelUser->getManager();

        return view('admin.advertiser.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager
        ]);
    }


    public function add($deal_id = null)
    {
        $model = new Advertiser();
        $modelDeal = new Deal();
        $modelUser = new User();
        $modelFrequency = new Frequency();
        $modelCurrency = new Currency();

        $dataCountry = Country::all();
        $dataState = State::all();
        $dataManager = $modelUser->getManager();
        $dataManagerAccount = $modelUser->getManagerAccount();
        $dataFrequency = $modelFrequency->getFrequency();
        $dataCurrency = $modelCurrency->getCurrency();

        if($deal_id){
            $dataDeal = $modelDeal::find($deal_id);
            $model->fill([
                'name' => $dataDeal->advertiser_name,
                'contact' => $dataDeal->advertiser_contact,
                'country' => $dataDeal->advertiser_country,
                'street1' => $dataDeal->advertiser_street1,
                'zip' => $dataDeal->advertiser_zip,
                'email' => $dataDeal->	advertiser_email,
                'phone' => $dataDeal->advertiser_phone,
                'currency_id' => $dataDeal->currency_id,
                'pipedrive_id' => $dataDeal->pd_organization_id,
            ]);

            $dataUser = User::where('pipedrive_id', $dataDeal->pd_user_id)->first();
            if($dataUser){
                $model->manager_id = $dataUser->id;
            }
        }

        $model->checkGoogleAccess();

        return view('admin.advertiser.add', [
            'data' => $model,
            'dataManager' => $dataManager,
            'dataManagerAccount' => $dataManagerAccount,
            'dataCountry' => $dataCountry,
            'dataState' => $dataState,
            'dataFrequency' => $dataFrequency,
            'dataCurrency' => $dataCurrency,
        ]);
    }


    public function edit($id)
    {
        $modelUser = new User();
        $modelFrequency = new Frequency();
        $modelCurrency = new Currency();

        $data = Advertiser::findOrFail($id);
        $dataCountry = Country::all();
        $dataState = State::all();
        $dataManager = $modelUser->getManager();
        $dataManagerAccount = $modelUser->getManagerAccount();
        $dataFrequency = $modelFrequency->getFrequency();
        $dataCurrency = $modelCurrency->getCurrency();

        return view('admin.advertiser.edit', [
            'data' => $data,
            'dataManager' => $dataManager,
            'dataManagerAccount' => $dataManagerAccount,
            'dataCountry' => $dataCountry,
            'dataState' => $dataState,
            'dataFrequency' => $dataFrequency,
            'dataCurrency' => $dataCurrency,
        ]);
    }


    public function saveEdit(Request $request)
    {

        $auth = Auth::user();

        $messageID = "";

        $this->validate($request, [
            'name' => 'required|max:255',
            'contact' => 'required|max:255',
            'manager_id' => 'required|integer',
            'manager_account_id' => 'nullable|integer',
            'prepay_amount' => 'nullable|required_with:prepay|integer',
            'street1' => 'required|max:255',
            'street2' => 'nullable|max:255',
            'city' => 'required|max:255',
            'state' => 'nullable|max:7',
            'country' => 'required|max:7',
            'currency' => 'required|integer',
            'province' => 'nullable|max:225',
            'zip' => 'required|max:63',
            'phone' => 'required|max:255',
            'email' => ['required', 'string', 'max:255', new EmailString()],
            'tracking_platform' => 'required_without_all:linktrust,everflow',
            'google_folder' => 'nullable|max:255',
            'cap' => 'nullable|integer',
            'frequency_id' => 'nullable|integer',
            'frequency_custom' => 'nullable|max:255|string',
            'ef_status' => 'required|max:31',
        ]);

        $modelInitPayment = new modelAdvertiserInitPayment();

        $data = Advertiser::findOrFail($request->id);

        $old['prepay'] = $data->prepay;
        $data->fill([
            'name' => $request->name,
            'contact' => $request->contact,
            'manager_id' => $request->manager_id,
            'manager_account_id' => $request->manager_account_id ? : 0,
            'prepay' => $request->prepay ? 1 : 0,
            'prepay_amount' => $request->prepay_amount ? : 0,
            'street1' => $request->street1,
            'street2' => $request->street2,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'currency_id' => $request->currency,
            'province' => $request->province,
            'zip' => $request->zip,
            'phone' => $request->phone,
            'email' => $request->email,
            'cap' => $request->cap,
            'frequency_id' => $request->frequency_id ? : 0,
            'frequency_custom' => $request->frequency_custom ? : null,
            'google_folder' => $request->google_folder,
            'ef_status' => $request->ef_status,
            'edited_by' => $auth->email,
            'edited_at' => date('Y-m-d H:i:s'),
        ]);

//        if($data->ef_id){
//            $EF_Advertiser = new EF_Advertiser();
//            $EF_result = $EF_Advertiser->updateAdvertiser($data);
//            if(!$EF_result){
//                return redirect()->route('admin.advertiser.edit', ['id' => $data->id])->with(['message' => [
//                    'type' => 'danger',
//                    'title' => 'Error!',
//                    'message' => "Advertiser has not been updated on EverFlow !",
//                    'autohide' => 0,
//                ]]);
//            }
//        }

        try{

            $data->save();

            $modelInitPayment->editAdvertiser($data, $old);

        } catch(PDOException $exception) {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $exception->getMessage(),
                'autohide' => 0,
            ]]);

        } catch(Exception $exception) {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $exception->getMessage(),
                'autohide' => 0,
            ]]);
        }

        return redirect()->route('admin.advertiser.edit', ['id' => $data->id])->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Advertiser $data->name has been updated !$messageID",
            'autohide' => 0,
        ]]);

    }


    public function saveAdd(Request $request)
    {
        $auth = Auth::user();

        $messageID = "";

        $this->validate($request, [
            'name' => 'required|max:255',
            'contact' => 'required|max:255',
            'manager_id' => 'required|integer',
            'manager_account_id' => 'required|integer',
            'prepay_amount' => 'nullable|required_with:prepay|integer',
            'street1' => 'required|max:255',
            'street2' => 'nullable|max:255',
            'city' => 'required|max:255',
            'state' => 'nullable|max:7',
            'country' => 'required|max:7',
            'currency' => 'required|integer',
            'province' => 'nullable|max:225',
            'zip' => 'required|max:63',
            'phone' => 'required|max:255',
            'email' => ['required', 'string', 'max:255', new EmailString()],
            'tracking_platform' => 'required_without_all:linktrust,everflow',
            'ef_status' => 'required_with:everflow|max:31',
            'frequency_id' => 'nullable|integer',
            'frequency_custom' => 'nullable|max:255|string',
            'pipedrive_id' => 'nullable|integer',
        ]);

        $modelInitPayment = new modelAdvertiserInitPayment();

        $data = new Advertiser();
        $data->fill([
            'name' => $request->name,
            'contact' => $request->contact,
            'manager_id' => $request->manager_id,
            'manager_account_id' => $request->manager_account_id,
            'prepay' => $request->prepay ? 1 : 0,
            'prepay_amount' => $request->prepay_amount ? : 0,
            'street1' => $request->street1,
            'street2' => $request->street2,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'currency_id' => $request->currency,
            'province' => $request->province,
            'zip' => $request->zip,
            'phone' => $request->phone,
            'email' => $request->email,
            'ef_status' => $request->ef_status,
            'frequency_id' => $request->frequency_id ? : 0,
            'frequency_custom' => $request->frequency_custom ? : null,
            'pipedrive_id' => $request->pipedrive_id ? : 0,
            'created_by' => $auth->email,
            'created_by_id' => $auth->id,
        ]);
        $data->createGoogleDriveFolder();

        /*
        if($request->linktrust) {
            $LT_Advertiser = new LT_Advertiser();
            $lt_id = $LT_Advertiser->createAdvertiser($data);
            if ($lt_id) {
                $data->lt_id = $lt_id;
                $messageID .= " LinkTrust id:$lt_id";
            } else {
                return redirect()->route('admin.advertiser.add')->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => "Advertiser has not been created on LinkTrust !",
                    'autohide' => 0,
                ]]);
            }
        }*/

        if($request->everflow){
            $EF_Advertiser = new EF_Advertiser();
            $ef_id = $EF_Advertiser->createAdvertiser($data);
            if ($ef_id) {
                $data->ef_id = $ef_id;
                $messageID .= " EverFlow id:$ef_id";
            } else {
                return redirect()->route('admin.advertiser.add')->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => "Advertiser has not been created on EverFlow !",
                    'autohide' => 0,
                ]]);
            }
        }

        try{

            $data->save();

            $modelInitPayment->addAdvertiser($data);

        } catch(Exception $exception) {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $exception->getMessage(),
                'autohide' => 0,
            ]]);
        }

        return redirect()->route('admin.advertiser.add')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Advertiser $data->name has been created !$messageID",
            'autohide' => 0,
        ]]);
    }

    public function ajaxGet(Request $request)
    {

        $columns     = ['a1.id', 'a1.lt_id', 'a1.ef_id', 'a1.name', 'a1.contact', 'a1.email', 'u.name', 'a1.created_at'];
        $columnsLike = ['a1.id', 'a1.lt_id', 'a1.ef_id', 'a1.name', 'a1.contact', 'a1.email', 'u.name', DB::raw('DATE_FORMAT(a1.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('advertiser as a1')
            ->select('a1.id', 'a1.lt_id', 'a1.ef_id', 'a1.name', 'a1.contact', 'a1.email', DB::raw('u.name AS manager'), DB::raw('DATE_FORMAT(a1.created_at, "%b %e, %Y %T") AS created_at'))
            ->leftJoin('users AS u', 'u.id', '=', 'a1.manager_id')
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
            $query->where('a1.manager_id', $request->manager);
        }
        if($request->duplicate_name){
            $query->join('advertiser as a2', 'a2.name', '=', 'a1.name')
                ->whereColumn('a2.id', '!=', 'a1.id');
        }

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('advertiser')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function profile($id = 0)
    {
        $modelNetwork = new modelNetwork();

        $data = Advertiser::where('id', $id)->first();
        $dataIO = modelIO::where('advertiser_id', $id)->get();

        return view('admin.advertiser.profile', [
            'count' => Advertiser::count(),
            'data' => $data,
            'dataIO' => $dataIO,
            'dataNetwork' => $modelNetwork->getNetwork(),
        ]);
    }


    public function missing()
    {
        $modelUser = new User();
        $model = new modelAdvertiserMissing();

        $dataManager = $modelUser->getManager();
        $dataManagerAccount = $modelUser->getManagerAccount();

        return view('admin.advertiser.missing', [
            'auth' => Auth::user(),
            'dataStatus' => $model->arrStatus,
            'dataManager' => $dataManager,
            'dataManagerAccount' => $dataManagerAccount,
        ]);
    }


    public function ajaxGetMissing(Request $request)
    {
        $columns     = ['am.ef_id', 'am.name', 'am.contact', 'am.email', 'u.name', 'u2.name', 'am.status', 'am.created_at'];
        $columnsLike = ['am.ef_id', 'am.name', 'am.contact', 'am.email', 'u.name', 'u2.name', 'am.status',  DB::raw('DATE_FORMAT(am.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('advertiser_missing as am')
            ->select('am.ef_id', 'am.name', 'am.contact', 'am.email', DB::raw('u.name AS manager'), DB::raw('u2.name AS manager_account'), 'am.status', DB::raw('DATE_FORMAT(am.created_at, "%b %e, %Y %T") AS created_at'), 'am.id')
            ->leftJoin('users AS u', 'u.id', '=', 'am.manager_id')
            ->leftJoin('users AS u2', 'u2.id', '=', 'am.manager_account_id')
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
            $query->where('am.manager_id', $request->manager);
        }
        if($request->manager_account){
            $query->where('am.manager_account_id', $request->manager_account);
        }
        if($request->status){
            $query->where('am.status', $request->status);
        }
        if($request->duplicate){
            $query->where('am.is_duplicate', 1);
        }

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('advertiser_missing')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function viewMissing($id)
    {
        $data = modelAdvertiserMissing::where('id', $id)->first();
        if($data){

            return view('admin.advertiser.viewmissing', [
                'data' => $data,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Advertiser not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function ignoreMissing($id)
    {
        $data = modelAdvertiserMissing::where('id', $id)->whereIn('status', [1])->first();
        if($data){

            $data->status = 2;
            $data->save();

            return redirect()->route('admin.advertiser.missing')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Advertiser has been ignored.",
                'autohide' => 0,
            ]]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Advertiser not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function addMissing($id)
    {
        $data = modelAdvertiserMissing::where('id', $id)->whereIn('status', [1, 2])->first();
        if($data){

            $modelUser = new User();
            $modelFrequency = new Frequency();
            $modelCurrency = new Currency();

            $dataCountry = Country::all();
            $dataState = State::all();
            $dataManager = $modelUser->getManager();
            $dataManagerAccount = $modelUser->getManagerAccount();
            $dataFrequency = $modelFrequency->getFrequency();
            $dataCurrency = $modelCurrency->getCurrency();

            return view('admin.advertiser.addmissing', [
                'data' => $data,
                'dataManager' => $dataManager,
                'dataManagerAccount' => $dataManagerAccount,
                'dataCountry' => $dataCountry,
                'dataState' => $dataState,
                'dataFrequency' => $dataFrequency,
                'dataCurrency' => $dataCurrency,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Advertiser not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }

    public function saveAddMissing(Request $request)
    {
        $this->validate($request, [
            'ef_id' => 'required|integer',
            'ef_status' => 'required_with:everflow|max:31',
            'name' => 'required|max:255',
            'contact' => 'required|max:255',
            'manager_id' => 'required|integer',
            'manager_account_id' => 'required|integer',
            'prepay_amount' => 'nullable|required_with:prepay|integer',
            'street1' => 'required|max:255',
            'street2' => 'nullable|max:255',
            'city' => 'required|max:255',
            'state' => 'nullable|max:7',
            'country' => 'required|max:7',
            'currency' => 'required|integer',
            'province' => 'nullable|max:225',
            'zip' => 'required|max:63',
            'phone' => 'required|max:255',
            'email' => ['required', 'string', 'max:255', new EmailString()],
            'frequency_id' => 'nullable|integer',
            'frequency_custom' => 'nullable|max:255|string',
        ]);

        $data = modelAdvertiserMissing::where('id', $request->id)->whereIn('status', [1, 2])->first();
        if($data){

            $modelInitPayment = new modelAdvertiserInitPayment();

            $auth = Auth::user();

            $data->status = 3;

            $advertiser = new Advertiser();
            $advertiser->fill([
                'name' => $request->name,
                'contact' => $request->contact,
                'email' => $request->email,
                'manager_id' => $request->manager_id,
                'manager_account_id' => $request->manager_account_id,
                'prepay' => 0,
                'prepay_amount' => 0,
                'street1' => $data->street1,
                'city' => $data->city,
                'state' => $data->state,
                'country' => $data->country,
                'currency_id' => $data->currency_id,
                'zip' => $data->zip,
                'ef_id' => $data->ef_id,
                'ef_status' => $data->ef_status,
                'created_by' => $auth->email,
                'created_by_id' => $auth->id,
            ]);
            $advertiser->createGoogleDriveFolder();

            DB::beginTransaction();

            try{

                $data->save();
                $advertiser->save();

                $modelInitPayment->addAdvertiser($advertiser);

                DB::commit();

            } catch(PDOException $exception) {

                DB::rollBack();

                return redirect()->back()->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $exception->getMessage(),
                    'autohide' => 0,
                ]]);

            } catch(Exception $exception) {

                DB::rollBack();

                return redirect()->back()->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $exception->getMessage(),
                    'autohide' => 0,
                ]]);
            }

            return redirect()->route('admin.advertiser.missing')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Advertiser has been added.",
                'autohide' => 0,
            ]]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Advertiser not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }
}
