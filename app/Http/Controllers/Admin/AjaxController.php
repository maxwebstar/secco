<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Advertiser;
use App\Models\IO;
use App\Models\Offer;
use DB;

use App\Services\EverFlow\Offer as EF_Offer;

use DateTime;
use PDOException;
use Validator;

class AjaxController extends Controller
{

    public function searchAdvertiser(Request $request)
    {
        $mysqlDB = DB::connection('mysql');
        $data = $mysqlDB->table('advertiser')
            ->select('id', DB::raw('CONCAT("(", '.$request->network.', ") ", name) AS text'))
            ->where('name', 'like', "%$request->search%")
            ->orWhere($request->network, 'like', "%$request->search%")
            ->get();

        return response()->json(['results' => $data], 200);
    }


    public function searchOffer(Request $request)
    {
        $mysqlDB = DB::connection('mysql');

        if(isset($request->only_campaign) && $request->only_campaign){

            $data = $mysqlDB->table('offer')
                ->select('id', DB::raw('CONCAT("(", '.$request->network.', ") ", campaign_name) AS text'))
                ->where(function($query) use ($request){
                    $query->where('campaign_name', 'like', "%$request->search%")
                        ->where($request->network, '>', 0);
                })
                ->orWhere(function($query) use ($request){
                    $query->where($request->network, 'like', "%$request->search%")
                        ->where($request->network, '>', 0);
                })
                ->get();

        } else {

            $data = $mysqlDB->table('offer')
                ->select('id', DB::raw('CONCAT("(", '.$request->network.', ") ", campaign_name) AS text'))
                ->where('campaign_name', 'like', "%$request->search%")
                ->orWhere($request->network, 'like', "%$request->search%")
                ->get();
        }

        return response()->json(['results' => $data], 200);
    }


    public function searchCountry(Request $request)
    {
        $mysqlDB = DB::connection('mysql');
        $data = $mysqlDB->table('country')
            ->select('key AS id', 'name AS text')
            ->where('name', 'like', "%$request->search%")
            ->get();

        return response()->json(['results' => $data], 200);
    }


    public function searchAffiliateByOffer(Request $request)
    {
        $mysqlDB = DB::connection('mysql');
        $data = $mysqlDB->table('affiliate as a')
            ->select(DB::raw("$request->key_id as id"), DB::raw('CONCAT("(", a.'.$request->network.', ") ", a.name) AS text'))
            ->where(function($query) use ($request){
                $query->where('a.name', 'like', "%$request->search%")
                    ->where('a.'.$request->network, '>', 0);
            })
            ->orWhere(function($query) use ($request){
                $query->where('a.'.$request->network, 'like', "%$request->search%")
                    ->where('a.'.$request->network, '>', 0);
            })
            ->join('offer_affiliate as o', function($query) use ($request){
                $query->on('o.offer_id', '=', DB::raw($request->offer_id))
                    ->on('o.affiliate_id', '=', 'a.id');
            })
            ->get();

        return response()->json(['results' => $data], 200);
    }


    public function searchCreativeByOffer(Request $request)
    {
        $mysqlDB = DB::connection('mysql');
        $query = $mysqlDB->table('offer_creative as oc')
            ->select(DB::raw("$request->field_id AS id"), DB::raw('CONCAT("(", oc.'.$request->network.', ") ", oc.name) AS text'))
            ->where('oc.offer_id', $request->offer_id)
            ->where(function($query) use ($request){
                $query->where('oc.name', 'like', "%$request->search%");
                    /*->where('oc.'.$request->network, '>', 0);*/
            })
            ->orWhere(function($query) use ($request){
                $query->where('oc.'.$request->network, 'like', "%$request->search%")
                    ->where('oc.offer_id', $request->offer_id);
                    /*->where('oc.'.$request->network, '>', 0);*/
            });

        $sql = $query->toSql();
        $data = $query->get();

        return response()->json(['results' => $data, 'sql' => $sql], 200);
    }


    public function searchCreativeByOfferWithoutNetwork(Request $request)
    {
        $mysqlDB = DB::connection('mysql');
        $query = $mysqlDB->table('offer_creative as oc')
            ->select('oc.id', DB::raw('CONCAT("(", oc.'.$request->network.', ") ", oc.name) AS text'))
            ->where('oc.offer_id', $request->offer_id)
            ->where('oc.name', 'like', "%$request->search%")
            ->where('oc.'.$request->network, 0);

        $sql = $query->toSql();
        $data = $query->get();

        return response()->json(['results' => $data, 'sql' => $sql], 200);
    }


    public function getAdvertiser(Request $request)
    {
        $mysqlDB = DB::connection('mysql');
        $dataAdvertiser = $mysqlDB->table('advertiser')
            ->select('name', 'phone', 'contact', 'email', 'country', 'state', 'city', 'street1', 'street2', 'zip', 'frequency_id', 'frequency_custom', 'currency_id', 'lt_id', 'ef_id', 'manager_id', 'manager_account_id')
            ->where('id', $request->advertiser_id)->first();

        $dataIO = $mysqlDB->table('io')
            ->select('id', 'google_file_name', 'google_file', 'google_folder', 'google_url', 'docusign_google_file', 'docusign_google_url',
                DB::raw('IF(google_created_at,  DATE_FORMAT(google_created_at, "%b %e, %Y"), "") as google_created_at'), 'gov_type', 'gov_date', 'governing', 'mongo_id')
            ->where('advertiser_id', $request->advertiser_id)
            //->where('status', 3)
            ->orderBy('created_at')->get();


        /*$dataManager = [];
        $dataManagerAccount = [];

        if($dataAdvertiser->manager_id) {
            $dataManager = $mysqlDB->table('users')
                ->select('name', 'email')
                ->where('id', $dataAdvertiser->manager_id)
                ->first();
        }
        if($dataAdvertiser->manager_account_id) {
            $dataManagerAccount = $mysqlDB->table('users')
                ->select('name', 'email')
                ->where('id', $dataAdvertiser->manager_account_id)
                ->first();
        }*/

        return response()->json(['advertiser' => $dataAdvertiser, 'io' => $dataIO/*, 'manager' => $dataManager, 'managerAccount' => $dataManagerAccount*/], 200);
    }


    public function getOffer(Request $request)
    {
        $mysqlDB = DB::connection('mysql');
        $dataOffer = $mysqlDB->table('offer')
            ->select('advertiser_contact', 'campaign_name', 'campaign_type', 'campaign_link', 'offer_category_id', 'domain_id',
                'pixel_id', 'pixel_location', 'redirect', 'redirect_url', 'cap_type_id', 'cap_unit_id',
                'cap_monetary', 'cap_lead', 'price_in', 'price_out', 'accepted_traffic', 'affiliate_note', 'internal_note',
                'need_api_lt', 'need_api_ef', 'lt_id', 'ef_id', 'ef_status', 'status')
            ->where('id', $request->offer_id)->first();

        return response()->json(['offer' => $dataOffer], 200);
    }


    public function getCreativeByRequest(Request $request)
    {
        $mysqlDB = DB::connection('mysql');
        $dataCreative = $mysqlDB->table('offer_creative')
            ->select('id', 'offer_id', 'request_id', 'iteration', 'name', 'link', 'price_in', 'price_out', 'lt_id', 'ef_id', 'status')
            ->where('request_id', $request->request_id)
            ->get();

        return response()->json(['creative' => $dataCreative], 200);
    }


    public function getTrackingByOffer(Request $request)
    {
        $dataOffer = Offer::find($request->offer_id);

        $EF_Offer = new EF_Offer();
        $dataTracking = $EF_Offer->getTracking($dataOffer->ef_id);

        return response()->json(['tracking' => $dataTracking], 200);
    }


    public function saveIOGovDate(Request $request)
    {
        $validator = Validator::make($request->only(['date']), [
            'date' => 'required|date'
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 200);
        }

        $data = IO::findOrFail($request->id);

        $govDate = new DateTime($request->date);
        $data->fill([
            'governing' => 1,
            'gov_type' => 'date',
            'gov_date' => $govDate->format("Y-m-d"),
        ]);

        DB::beginTransaction();

        try {

            if($data->advertiser_id){
                DB::table('io')
                    ->where('advertiser_id', $data->advertiser_id)
                    ->update(['governing' => 0]);
            }

            $data->save();

            DB::commit();

        } catch (PDOException $e) {

            DB::rollBack();

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }

        return response()->json(['status' => 'ok', 'message' => "Insertion order $data->google_file_name has been update"], 200);
    }
}
