<?php
namespace App\Services\QB;

use QuickBooksOnline\API\DataService\DataService;
use App\Models\QB\Access as modelAccess;

use Carbon\Carbon;

class Core
{

    protected $dataService;


    public function __construct($login = false)
    {
        $param = $this->getAccessParam($login);

        // Prep Data Services
        $dataService = DataService::Configure($param);

        $this->dataService = $dataService;
    }


    public function getDataService()
    {
        return $this->dataService;
    }


    public function getAccessParam($login = false)
    {
        $dataAccess = modelAccess::find(1);
        if($dataAccess && $login == false){

            $dateValidRefresh = Carbon::parse($dataAccess->updated_at)->addSecond($dataAccess->refresh_token_expires_in);
            $dateValidAccess = Carbon::parse($dataAccess->updated_at)->addSecond($dataAccess->expires_in);
            $dateNow = Carbon::now();

            if($dateValidAccess > $dateNow){

                $param = [
                    'auth_mode' => 'oauth2',
                    'ClientID' => config('services.qb.client_id'),
                    'ClientSecret' => config('services.qb.client_secret'),
                    'RedirectURI' => config('services.qb.redirect_url'),
                    'scope' => config('services.qb.scope'), /* or com.intuit.quickbooks.payment*/
                    'baseUrl' => config('services.qb.base_url'),

                    'accessTokenKey' => $dataAccess->access_token,
                    'refreshTokenKey' => $dataAccess->refresh_token,
                    'QBORealmID' => $dataAccess->real_m_id,
                ];

            } elseif($dateValidRefresh > $dateNow){

                $param = [
                    'auth_mode' => 'oauth2',
                    'ClientID' => config('services.qb.client_id'),
                    'ClientSecret' => config('services.qb.client_secret'),
                    'RedirectURI' => config('services.qb.redirect_url'),
                    'scope' => config('services.qb.scope'), /* or com.intuit.quickbooks.payment*/
                    'baseUrl' => config('services.qb.base_url'),

                    'refreshTokenKey' => $dataAccess->refresh_token,
                    'QBORealmID' => $dataAccess->real_m_id,
                ];

                $this->refreshToken($dataAccess, $param);

            } else {

                $param = [
                    'auth_mode' => 'oauth2',
                    'ClientID' => config('services.qb.client_id'),
                    'ClientSecret' => config('services.qb.client_secret'),
                    'RedirectURI' => config('services.qb.redirect_url'),
                    'scope' => config('services.qb.scope'), /* or com.intuit.quickbooks.payment*/
                    'baseUrl' => config('services.qb.base_url'),
                ];
            }

        } else {

            $param = [
                'auth_mode' => 'oauth2',
                'ClientID' => config('services.qb.client_id'),
                'ClientSecret' => config('services.qb.client_secret'),
                'RedirectURI' => config('services.qb.redirect_url'),
                'scope' => config('services.qb.scope'), /* or com.intuit.quickbooks.payment*/
                'baseUrl' => config('services.qb.base_url'),
            ];
        }

        return $param;
    }


    public function refreshToken(modelAccess &$dataAccess, &$param)
    {
        $dataService = DataService::Configure($param);

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();

        $dataAccess->fill([
            'access_token' => $refreshedAccessTokenObj->getAccessToken(),
            'expires_in' => $refreshedAccessTokenObj->getAccessTokenValidationPeriodInSeconds(),
            'refresh_token' => $refreshedAccessTokenObj->getRefreshToken(),
            'refresh_token_expires_in' => $refreshedAccessTokenObj->getRefreshTokenValidationPeriodInSeconds(),
        ]);
        $dataAccess->save();

        $param['accessTokenKey'] = $dataAccess->access_token;
        $param['refreshTokenKey'] = $dataAccess->refresh_token;

//        unset($param['scope']);
//        unset($param['RedirectURI']);
    }
}