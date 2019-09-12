<?php
namespace App\Services;

use Google_Client;
use Google_Service_Oauth2;

use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleCron
{

    protected $google_client;

    public function __construct()
    {
        $googleClient = new Google_Client();
        $googleClient->setClientId(config('services.google_service_account.client_id'));
        $googleClient->setScopes(config('services.google_service_account.scope'));
        $googleClient->setAuthConfig(storage_path("/" . config('services.google_service_account.file')));

        $this->google_client = $googleClient;
    }


    public function getClient()
    {
        return $this->google_client;
    }

    public function getMetadataXlsx($fileName, array $parentFolderID)
    {
        if(empty($fileName)){
            throw new Exception('Error: GoogleDrive File name is empty.');
        }

        $metaData = new Google_Service_Drive_DriveFile(array(
            'name' => $fileName,
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));

        if($parentFolderID){
            $metaData->setParents($parentFolderID);
        }

        return $metaData;
    }

}