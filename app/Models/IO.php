<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Exception;

class IO extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'io';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['campaign_name',	'advertiser_id', 'currency_id',
        'compCpc',	'compCpa', 'compCpl', 'compCpm', 'compCpd',	'compCpi', 'compCps',
        'traffic_search',	'traffic_banner', 'traffic_popup', 'traffic_context', 'traffic_exit', 'traffic_incent',	'traffic_path',	'traffic_social', 'traffic_mobile', 'traffic_email', 'traffic_incent_name',
        'secco_contact', 'secco_email', 'secco_phone', 'secco_fax', 'manager_account_id',
        'company_name', 'company_contact',	'company_email', 'company_country',	'company_state', 'company_city', 'company_street1',	'company_street2', 'company_zip', 'company_phone', 'company_fax',
        'billing_contact', 'billing_street1', 'billing_street2', 'billing_country',	'billing_state', 'billing_city', 'billing_zip',
        'prepay', 'prepay_amount',
        'gov_type',	'gov_date',	'governing', 'governing_term', 'status',
        'google_created_at', 'google_url', 'google_folder', 'google_file', 'google_file_name', 'file_pdf_exist',
        'term_id', 'mobile_attribut_platform', 'template_document_id', 'template_document_custom',	'note', 'frequency_id', 'frequency_custom', 'pipedrive_id',
        'docusign_email_advertiser', 'docusign_name_advertiser', 'docusign_manager_id', 'docusign_id', 'docusign_file', 'docusign_google_file', 'docusign_google_url',
        'restricted_no_adult', 'restricted_no_incent', 'restricted_no_rebrokering', 'restricted_no_affiliate_net', 'restricted_none',
        'time', 'credit', 'credit_local_file', 'created_by', 'created_by_id', 'created_at',
        'order_number', 'mongo_user_id', 'mongo_id',
    ];

    /**
     * gov_type
     *
     * - new
     * - date
     */

    /**
     * Status for io
     *
     * 1 - New
     * 2 - Declined
     * 3 - Approved
     * 4 - Out via Docusign
     * 5 - Duplicate
     */

    public $arrStatus = [
        1 => "New",
        2 => "Declined",
        3 => "Approved",
        4 => "Out via Docusign",
        5 => "Duplicate",
        6 => "Waiting for Signature",
    ];

    public function getStatus()
    {
        return isset($this->arrStatus[$this->status]) ? $this->arrStatus[$this->status] : "None";
    }

    public $path_docx = "io/docx/result/";
    public $path_pdf = "io/pdf/result/";
    public $path_credit_docx = "io/docx/credit/";
    public $path_credit_pdf = "io/pdf/credit/";
    public $path_docusign = "io/docusign/";

    /**
     * Get country.
     *
     * @var Eloquent
     */
    public function company_country_param()
    {
        return $this->hasOne('App\Models\Country', 'key', 'company_country');
    }
    public function billing_country_param()
    {
        return $this->hasOne('App\Models\Country', 'key', 'billing_country');
    }

    /**
     * Get state.
     *
     * @var Eloquent
     */
    public function company_state_param()
    {
        return $this->hasOne('App\Models\State', 'key', 'company_state');
    }
    public function billing_state_param()
    {
        return $this->hasOne('App\Models\State', 'key', 'billing_state');
    }

    /**
     * Get Template Document.
     *
     * @var Eloquent
     */
    public function template_document()
    {
        return $this->hasOne('App\Models\IOTemplateDoc', 'id', 'template_document_id')->withDefault([
            'name' => '',
            'file_name' => '',
        ]);
    }

    /**
     * Get Currency.
     *
     * @var Eloquent
     */
    public function currency()
    {
        return $this->hasOne('App\Models\Currency', 'id', 'currency_id');
    }

    /**
     * Get advertiser.
     *
     * @var Eloquent
     */
    public function advertiser()
    {
        return $this->hasOne('App\Models\Advertiser', 'id', 'advertiser_id')->withDefault([
            'name' => '',
        ]);
    }

    /**
     * Get author.
     *
     * @var Eloquent
     */
    public function created_param()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by_id');
    }

    /**
     * Get docusign manager.
     *
     * @var Eloquent
     */
    public function docusign_manager()
    {
        return $this->hasOne('App\Models\User', 'id', 'docusign_manager_id');
    }

    /**
     * Get frequency.
     *
     * @var Eloquent
     */
    public function frequency()
    {
        return $this->hasOne('App\Models\Frequency', 'id', 'frequency_id');
    }

    /**
     * Get term.
     *
     * @var Eloquent
     */
    public function term()
    {
        return $this->hasOne('App\Models\TermTemplate', 'id', 'term_id');
    }

    /**
     * Get manager.
     *
     * @var Eloquent
     */
    public function manager_account()
    {
        return $this->hasOne('App\Models\User', 'id', 'manager_account_id');
    }

    /**
     * Get traffic pcc.
     *
     * @var Eloquent
     */
    public function traffic_ppc()
    {
        return $this->belongsToMany('App\Models\TrafficPpc', 'io_traffic_ppc', 'io_id', 'traffic_id')->withTimestamps();
    }

    public function createGoogleDriveDocx()
    {
        $managerFolder = $this->advertiser->google_folder;

        if($this->google_file || $managerFolder == false){
            return false;
        }

        $this->google_folder = $managerFolder;

        $googleDrive = new \App\Services\GoogleDrive();
        $googleDriveService = $googleDrive->getService();

        $fileMetadata = $googleDrive->getMetadataDocx($this->google_file_name, [$managerFolder]);

        $content = file_get_contents(public_path($this->path_docx . $this->google_file_name . ".docx"));
        $param = array(
            'data' => $content,
            'mimeType' => 'application/msword',
            'uploadType' => 'multipart',
            'fields' => 'id');



        $result = $googleDriveService->files->create($fileMetadata, $param);
        if(isset($result->id)){

            $this->google_file = $result->id;
            $this->google_url = "https://drive.google.com/open?id=$result->id";
            $this->google_created_at = date('Y-m-d H:i:s');

            return true;
        }

        return false;
    }


    public function createGoogleDrivePdf()
    {
        if($this->google_folder == false){
            return false;
        }

        $googleDrive = new \App\Services\GoogleDrive();
        $googleDriveService = $googleDrive->getService();

        $fileMetadata = $googleDrive->getMetadataPdf($this->docusign_file, [$this->google_folder]);

        $content = file_get_contents(public_path($this->path_docusign . $this->docusign_file . ".pdf"));
        $param = array(
            'data' => $content,
            'mimeType' => 'application/pdf',
            'uploadType' => 'multipart',
            'fields' => 'id');

        $result = $googleDriveService->files->create($fileMetadata, $param);
        if(isset($result->id)){

            $this->docusign_google_file = $result->id;
            $this->docusign_google_url = "https://drive.google.com/open?id=$result->id";

            return true;
        }

        return false;
    }


    public function deleteGoogleDrivePdf()
    {
        if($this->docusign_google_file == false){
            return ['status' => 'success'];
        }

        $googleDrive = new \App\Services\GoogleDrive();
        $googleDriveService = $googleDrive->getService();

        try {

            $googleDriveService->files->delete($this->docusign_google_file);

        }  catch (Exception $e) {

            return ['status' => 'error', 'message' => $e->getMessage()];
        }

        $this->docusign_google_file = null;
        $this->docusign_google_url = null;

        return ['status' => 'success'];
    }


    public function getTemplateDocumet()
    {
        if($this->template_document_id == 2 && $this->gov_type == 'new'){
            $template = \App\Models\IOTemplateDoc::findOrFail(2); /*IOTemplate_n30.docx*/
        }else{
            if($this->gov_type == 'date'){
                $template = \App\Models\IOTemplateDoc::findOrFail(3); /*IOTemplate_gov.docx*/
            } else {
                $template = \App\Models\IOTemplateDoc::findOrFail(1); /*IOTemplate.docx*/
            }
        }

        return $template;
    }

    public function getAdditionPathForTemplate()
    {
        $path = "";
        $is_traffic = "0";
        $is_ppc = "0";
        $is_mobile = "0";

        if($this->traffic_search ||
            $this->traffic_banner ||
            $this->traffic_popup ||
            $this->traffic_context ||
            $this->traffic_exit ||
            $this->traffic_email ||
            $this->traffic_path ||
            $this->traffic_social ||
            $this->traffic_mobile){
            $is_traffic = "1";
        }
        if(count($this->traffic_ppc)){
            $is_ppc = "1";
        }
        if($this->mobile_attribut_platform){
            $is_mobile = "1";
        }

        $case = (string) $is_traffic . (string) $is_ppc . (string) $is_mobile;

        switch($case) {
            case "000" : $path = "no_traffic_ppc_mobile/"; break;
            case "001" : $path = "no_traffic_ppc/"; break;
            case "010" : $path = "no_traffic_mobile/"; break;
            case "100" : $path = "no_ppc_mobile/"; break;
            case "011" : $path = "no_traffic/"; break;
            case "101" : $path = "no_ppc/"; break;
            case "110" : $path = "no_mobile/"; break;
            case "111" : $path = ""; break;
            default : $path = ""; break;
        }

        return $path;
    }


}
