<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\QB\Access as modelQBAccess;

use App\Services\QB\Core as QB_Core;

class QBController extends Controller
{

    public function login()
    {
        $qbCore = new QB_Core(true);
        $dataService = $qbCore->getDataService();

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

        $url = $OAuth2LoginHelper->getAuthorizationCodeURL();

        return redirect($url);
    }


    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {

        $qbCore = new QB_Core(true);
        $dataService = $qbCore->getDataService();

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

        if($request->code){

            $accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($request->code, $request->realmId);

            $data = modelQBAccess::find(1);
            if(!$data){
                $data = new modelQBAccess();
            }

            $data->fill([
                'real_m_id' => $accessTokenObj->getRealmID(),
                'access_token' => $accessTokenObj->getAccessToken(),
                'refresh_token' => $accessTokenObj->getRefreshToken(),
                'expires_in' => $accessTokenObj->getAccessTokenValidationPeriodInSeconds(),
                'refresh_token_expires_in' => $accessTokenObj->getRefreshTokenValidationPeriodInSeconds(),
            ]);
            $data->save();

            return redirect()->route('admin.dashboard')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "QB access params has been setted",
                'autohide' => 1,
            ]]);
        }
    }
}
