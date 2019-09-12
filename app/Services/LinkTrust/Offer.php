<?php

namespace App\Services\LinkTrust;

use Exception;
use App\Models\Offer as modelOffer;
use App\Models\Country as modelCountry;

class Offer extends Core
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getStat($dateStart, $dateEnd, $filter = 'Traffic')
    {
        $params = array(
            'DateFrom' => $dateStart,
            'DateTo' => $dateEnd,
            'TrafficFilter' => $filter,
            'Status' => ''
        );

        $url = $this->createV1Link('/rest/'.$this->api_id.'/reports/campaignperformance.xml', $params);

        $result = $this->curlGet($url);
        $xml = simplexml_load_string($result);

        if($xml->Campaign){
            return $xml;
        } else {
            return false;
        }
    }


    public function createJSON(modelOffer $data)
    {
        $payload = array();
        $payload['Campaign'] = array();

        $domains = array('srv' => 'srvbytrking', 'srv2' => 'srv2trking', 'sat' => 'satrk', 'sq2' => 'sq2trk2', 'srvby' => 'srvbytrking');
        $domain = isset($domains[$data->domain->value]) ? $domains[$data->domain->value] : "";

        $pixelTypes = array('ServerSide', 'mindspark', 'Image', 'simg', 'JavaScript', 'sjs', 'sServerSide');
        $cookieTrackingPixels = array('ServerSide', 'mindspark');
        $clickid_postbackPixels = array('ServerSide', 'mindspark');
        $imagePixels = array('Image', 'simg', 'JavaScript', 'sjs');
        $imagePixelsStrict = array('Image', 'simg');
        $jsPixels = array('JavaScript', 'sjs');
        $iframePixels = array('JavaScript', 'sjs');
        $additionalCodePixels = array('JavaScript', 'sjs');
        $securePixels = array('sServerSide', 'sjs', 'simg');

        $manager_lt_id = $data->manager->lt_id;
        $payload['Campaign']['CampaignName'] = addslashes($data->campaign_name);
        $payload['Campaign']['Type'] = strtoupper($data->campaign_type);
        $payload['Campaign']['Category'] = $data->offer_category->name;
        $payload['Campaign']['CampaignStatus'] = 'Testing';
        $payload['Campaign']['MerchantId'] = $data->advertiser->lt_id;
        $payload['Campaign']['Revenue'] = $data->price_in ? : 0;
        $payload['Campaign']['RevenueType'] = 'Flat';
        $payload['Campaign']['DefaultAffiliatePayout'] = $data->price_out;
        $payload['Campaign']['DefaultAffiliatePayoutType'] = 'Flat';
        $payload['Campaign']['AutoApproveTransactionForAllAffiliates'] = true;
        $payload['Campaign']['DefaultLandingPageURL'] = $data->campaign_link;
        $payload['Campaign']['DefaultLandingPageHideAffiliateReferrers'] = 'true';
        $payload['Campaign']['AllowClickIDPostbacks'] = (in_array($data->pixel->key, $clickid_postbackPixels)) ? 'true' : 'false';
        $payload['Campaign']['AllowCookieTracking'] = (in_array($data->pixel->key, $cookieTrackingPixels)) ? 'false' : 'true';
        $payload['Campaign']['AllowCookielessTracking'] = 'true';
        $payload['Campaign']['Affiliates'] = array(100017);
        if($manager_lt_id){
            $payload['Campaign']['CampaignManagers'] = [$manager_lt_id];
        }

        $payload['Filters']['TrafficCapRedirectType'] = 'URL';
        $payload['Filters']['TrafficCapRedirectUrl'] = htmlspecialchars($data->redirect_url);
        if($data->geos){
            $arrCountryCode = explode(',', $data->geos);
            foreach($arrCountryCode as $code){
                $country = modelCountry::where('key', $code)->first();
                if($country){
                    $payload['Filters']['Countries'][] = $country->name;
                }
            }
        }
        $payload['Filters']['UnauthorizedCountryRedirectType'] = 'LandingPage';
        $payload['Notes']['Notes'] = $data->internal_note;

        $affiliateNotes = preg_replace("/[^A-Za-z0-9 ]/", '', $data->affiliate_note);
        $pixelProtocol = (in_array($data->pixel->key, $securePixels)) ? 'https://' : 'http://';

        $payload['AffiliateCenterSettings']['Description'] = $affiliateNotes;
        $payload['AffiliateCenterSettings']['ImpressionUrl'] = "{$pixelProtocol}{$domain}.com/impression.track";
        $payload['AffiliateCenterSettings']['TrackingUrl'] = "{$pixelProtocol}{$domain}.com/click.track";
        $payload['AffiliateCenterSettings']['ImpressionUrlFormat'] = 'Custom';
        $payload['AffiliateCenterSettings']['TrackingUrlFormat'] = 'Custom';

        $payload['Pixel']['PixelType'] = (in_array($data->pixel->key, $jsPixels)) ? 'JavaScript' : ((in_array($data->pixel->key, $imagePixelsStrict)) ? 'Image' : 'ServerSide');
        $payload['Pixel']['AllowAffiliateToManageTheirPixel'] = 'true';
        $payload['Pixel']['AllowAffiliateToPlaceServerSidePixels'] = 'true';
        $payload['Pixel']['AllowAffiliateToPlaceImagePixel'] = (in_array($data->pixel->key, $imagePixels)) ? 'true' : 'false';
        $payload['Pixel']['AllowAffiliatesToPlaceJavaScriptPixels'] = (in_array($data->pixel->key, $jsPixels)) ? 'true' : 'false';
        $payload['Pixel']['AllowAffiliateToPlaceIFramePixels'] = (in_array($data->pixel->key, $iframePixels)) ? 'true' : 'false';
        $payload['Pixel']['AllowAffiliatesToAddAdditionalCode'] = (in_array($data->pixel->key, $additionalCodePixels)) ? 'true' : 'false';
        $payload['Pixel']['RequireSecurePixelURLs'] = (in_array($data->pixel->key, $securePixels)) ? 'true' : 'false';

        return $payload;
    }


    public function createOffer(modelOffer $data)
    {

        $url = "https://api.linktrust.com/v2/Campaigns/Campaign";
        $dataJson = $this->createJSON($data);

        $dataJson = json_encode($dataJson);

        $user = "seccoadmin:Seccosquared1";
        try {
            // Get cURL resource
            $curl = curl_init();

            // Set some options - we are passing in a useragent too here

            curl_setopt_array($curl, array(

                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_SSL_VERIFYPEER => FALSE,
                    CURLOPT_SSL_VERIFYHOST => FALSE,
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_USERPWD => $user,
                    CURLOPT_HTTPHEADER => array("Content-Type: application/json"),
                    CURLOPT_CUSTOMREQUEST => "PUT",
                    CURLOPT_POSTFIELDS => $dataJson
                )
            );

            // Send the request & save response to $resp
            $resp = curl_exec($curl);
            // Close request to clear up some resources
            curl_close($curl);

            if (FALSE === $resp)
                throw new Exception(curl_error($curl), curl_errno($curl));

        } catch (Exception $e) {
            throw new Exception('Error: Curl failed ' . $e->getMessage());
        }

        if (strpos($resp, 'Successfully added campaign')) {
            $lt_id = filter_var($resp, FILTER_SANITIZE_NUMBER_INT);
            return ['lt_id' => $lt_id];
        } else {
            return ['lt_id' => 0, 'message' => $resp];
        }
    }

}