<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Offer as modelOffer;
use App\Models\Currency as modelCurrency;
use App\Models\Request\Price as modelRequestPrice;

use DB;
use App\Services\EverFlow\Offer as EF_Offer;

use PDOException;
use Exception;

class FXRateController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:accounting_section_fxrate'], ['only' => ['index', 'ajax-get-campaign', 'ajax-update-price']]);
    }


    public function index()
    {
        $dataEuro = modelCurrency::where('id', 2)->first();
        $dataPound = modelCurrency::where('id', 3)->first();

        return view('admin.fxrate.index', [
            'dataEuro' => $dataEuro,
            'dataPound' => $dataPound,
        ]);
    }


    public function ajaxGetCampaign(Request $request)
    {
        $mysqlDB = DB::connection('mysql');
        $sql = $mysqlDB->table('offer AS o')
            ->select('o.id', 'o.lt_id', 'o.ef_id', 'o.campaign_name', 'a.currency_id', 'o.price_in', 'o.price_out')
            ->join('advertiser AS a', 'a.id', '=', 'o.advertiser_id')
            ->where('a.currency_id', $request->currency_id)
            ->where('o.ef_id', '>', 0)
            ->where('o.ef_status', 'active');
            /*->where(function($query){
                $query->where('o.lt_id', '>', 0)
                    ->orWhere('o.ef_id', '>', 0);
            });*/

        $dataCurrency = modelCurrency::where('id', $request->currency_id)->first();
        $dataCurrency->rate = round($request->rate, 2);

        DB::beginTransaction();

        try {

            $dataCurrency->save();

            $data = $sql->get();

            DB::commit();

        } catch (PDOException $e){

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'alert' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'hide' => 0,
                ]

            ], 400);
        }

        return response()->json(['status' => 'ok', 'results' => $data], 200);
    }


    public function ajaxUpdatePrice(Request $request)
    {
        $auth = Auth::user();

        $type = $request->rate > 1 ? 1 : 2;

        $price_in = round($request->offer['price_in'] * $request->rate, 2);
        $price_out = $request->offer['price_out'];

        $dataOffer = modelOffer::where('id', $request->offer['id'])->first();

        if(!$request->offer['ef_id']){

            return response()->json([
                'status' => 'error',
                'price_in' => $price_in,
                'price_out' => $price_out,
                'alert' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => "Campaigns " . $request->offer['campaign_name'] . " don't have everflow id.",
                    'hide' => 0,
                ]

            ], 200);
        }
        if(!$dataOffer){

            return response()->json([
                'status' => 'error',
                'price_in' => $price_in,
                'price_out' => $price_out,
                'alert' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => "Campaigns " . $request->offer['campaign_name'] . " not found.",
                    'hide' => 0,
                ]

            ], 200);
        }

        $dataPrice = new modelRequestPrice();
        $dataPrice->fill([
            'network_id' => 2,
            'offer_id' => $request->offer['id'],
            'affiliate_all' => 1,
            'affiliate_id' => 0,
            'date' => date('Y-m-d'),
            'price_in' => $price_in,
            'price_out' => $price_out,
            'current_price_in' => $request->offer['price_in'],
            'current_price_out' => $request->offer['price_out'],
            'type' => $type,
            'cap_change' => 0,
            'reason' => "change fx rate",
            'status' => 1,
            'is_fx_rate' => 1,
            'ef_id' => 0,
            'created_by' => $auth->email,
            'created_by_id' => $auth->id,
        ]);


//        $ef_Offer = new EF_Offer();
//        $ef_resp = $ef_Offer->updateOfferPrice($dataPrice);
//
//        if($ef_resp['ef_id']){
//            $dataPrice->ef_id = $ef_resp['ef_id'];
//            $dataPrice->status = 3;
            $dataPrice->save();

            $dataOffer->price_in = $price_in;
            $dataOffer->save();

            return response()->json([
                'status' => 'ok',
                'price_in' => $price_in,
                'price_out' => $price_out
            ], 200);

//        } else {
//            $dataPrice->error_api = $ef_resp['message'];
//            $dataPrice->save();
//
//            return response()->json([
//                'status' => 'error',
//                'price_in' => $price_in,
//                'price_out' => $price_out,
//                'alert' => [
//                    'type' => 'danger',
//                    'title' => 'Error!',
//                    'message' => "EF id = " . $request->offer['ef_id'] . " " . $ef_resp['message'],
//                    'hide' => 0,
//                ]
//
//            ], 200);

//        }

    }



}