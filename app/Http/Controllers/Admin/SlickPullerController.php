<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\EverFlow\Offer as EF_Offer;

use App\Models\Network as modelNetwork;
use App\Models\Offer as modelOffer;
use App\Models\OfferUrl as modelOfferUrl;
use App\Models\Domain as modelDomain;
use DB;

class SlickPullerController extends Controller
{
    public function index()
    {
        $modelNetwork = new modelNetwork();

        $dataNetwork = $modelNetwork->getNetwork();

        return view('admin.slickpuller.index', [
            'dataNetwork' => $dataNetwork,
        ]);
    }


    public function ajaxGetDataLT(Request $request)
    {
        $mysqlDB = DB::connection('mysql');
        $dataOffer = $mysqlDB->table('offer')
            ->select('advertiser_contact', 'campaign_name', 'campaign_type', 'campaign_link', 'offer_category_id', 'domain_id',
                'pixel_id', 'pixel_location', 'redirect', 'redirect_url', 'cap_type_id', 'cap_unit_id',
                'cap_monetary', 'cap_lead', 'price_in', 'price_out', 'accepted_traffic', 'affiliate_note', 'internal_note',
                'need_api_lt', 'need_api_ef', 'lt_id', 'ef_id', 'ef_status', 'status')
            ->where('id', $request->offer_id)->first();

        $dataDomain = $mysqlDB->table('domain')
            ->select('ef_id', 'value', 'name', 'show', 'position')
            ->where('id', $dataOffer->domain_id)->first();

        return response()->json(['offer' => $dataOffer, 'domain' => $dataDomain], 200);
    }


    public function ajaxGetDataEF(Request $request)
    {
        $dataOffer = modelOffer::where('id', $request->offer_id)->first();

        if($dataOffer){
            $dataOffer->updateEFUrl();
            $dataUrl = $dataOffer->offer_url;
        } else {
            $dataUrl = [];
        }

        return response()->json(['offer' => $dataOffer, 'url' => $dataUrl], 200);
    }


    public function ajaxGetTrackingUrl(Request $request)
    {
        $EF_Offer = new EF_Offer();
        $data = $EF_Offer->getTrackingAffiliate($request->offer_ef_id, $request->affiliate_ef_id, $request->url_ef_id);

        $result = isset($data->url) ? $data->url : "";

        return response()->json(['url' => $result], 200);

    }
}
