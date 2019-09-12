<?php
namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

use App\Services\Google;

use Illuminate\Support\Facades\Auth;


class GoogleDrive extends Google{

    public function __construct(){

        parent::__construct();

    }

    public function getService()
    {
        $this->checkAccessToken();

        if($this->google_client->getAccessToken()) {
            return new Google_Service_Drive($this->google_client);
        } else {
            throw new Exception('Error: Google AccessToken not exist.');
        }
    }

    public function getMetadata($folderName, array $parentFolderID)
    {
        if(empty($folderName)){
            throw new Exception('Error: GoogleDrive Folder name is empty.');
        }

        $metaData = new Google_Service_Drive_DriveFile(array(
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder'));

        if($parentFolderID){
            $metaData->setParents($parentFolderID);
        }

        return $metaData;
    }

    public function getMetadataDocx($fileName, array $parentFolderID)
    {
        if(empty($fileName)){
            throw new Exception('Error: GoogleDrive File name is empty.');
        }

        $metaData = new Google_Service_Drive_DriveFile(array(
            'name' => $fileName,
            'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'));

        if($parentFolderID){
            $metaData->setParents($parentFolderID);
        }

        return $metaData;
    }

    public function getMetadataPdf($fileName, array $parentFolderID)
    {
        if(empty($fileName)){
            throw new Exception('Error: GoogleDrive File name is empty.');
        }

        $metaData = new Google_Service_Drive_DriveFile(array(
            'name' => $fileName,
            'mimeType' => 'application/pdf'));

        if($parentFolderID){
            $metaData->setParents($parentFolderID);
        }

        return $metaData;
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

