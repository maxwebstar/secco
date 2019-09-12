<?php
namespace App\Services;

use PhpOffice\PhpWord\Settings;
use App\Models\Advertiser;
use App\Models\IOTemplateDoc;
use App\Models\IO;
use App\Models\TrafficPpc;

use Exception;

class PhpWord
{

    protected $writers;

    public function __construct()
    {
        Settings::loadConfig();

        $this->writers = array('Word2007' => 'docx', 'ODText' => 'odt', 'RTF' => 'rtf', 'HTML' => 'html', 'PDF' => 'pdf');

        if (null === Settings::getPdfRendererPath()) {
            $this->writers['PDF'] = null;
        }
    }


    public function createDocx(IO $data)
    {
        $template = $data->getTemplateDocumet();
        $path_add = $data->getAdditionPathForTemplate();

        $file = public_path($template->path_docx . $path_add . $template->file_name);

        if(file_exists($file) == false){
            throw new Exception('Error: Template file not exist.');
        }

        $document = new \PhpOffice\PhpWord\TemplateProcessor($file);

        $this->setTemplateValues($data, $document);

        $document->saveAs(public_path($data->path_docx . $data->google_file_name . ".docx"));
    }


    protected function setTemplateValues($data, &$document) {

        $dataTrafficPpc = TrafficPpc::orderBy('position')->get();

        $manager_account = $data->manager_account;

        $company_state_param = $data->company_state_param;
        $company_country_param = $data->company_country_param;

        $billing_state_param = $data->billing_state_param;
        $billing_country_param = $data->billing_country_param;

        $template_document = $data->template_document;

        $traffic_ppc = [];
        $tmp = $data->traffic_ppc;
        if($tmp){
            foreach($tmp as $iter){
                $traffic_ppc[$iter->id] = $iter;
            }
        }

        $document->setValue('orderNumber', $data->order_number);

        $document->setValue('company_name', $data->company_name);
        $document->setValue('company_contact', preg_replace("/&#?[a-z0-9]{2,8};/i","", $data->company_contact));
        $document->setValue('company_phone', $data->company_phone);
        $document->setValue('company_fax', $data->company_fax);
        $document->setValue('company_email', $data->company_email);
        $document->setValue('company_address', $data->company_street1);
        $document->setValue('company_address2', $data->company_street2);
        $document->setValue('company_city', $data->company_city);
        $document->setValue('company_state', $company_state_param ? $company_state_param->name : "");
        $document->setValue('company_country', $company_country_param ? $company_country_param->name : "");
        $document->setValue('company_zip', $data->company_zip);

        $document->setValue('account_manager', $manager_account ? $manager_account->name : "");
        $document->setValue('secco_contact', $data->secco_contact);
        $document->setValue('secco_phone', $data->secco_phone);
        $document->setValue('secco_fax', $data->secco_fax);
        $document->setValue('secco_email', $data->secco_email);

        $document->setValue('billing_contact', $data->billing_contact);
        $document->setValue('billing_address', $data->billing_street1);
        $document->setValue('billing_address2', $data->billing_street2);
        $document->setValue('billing_city', $data->billing_city);
        $document->setValue('billing_state', $billing_state_param ? $billing_state_param->name : "");
        $document->setValue('billing_country', $billing_country_param ? $billing_country_param->name : "");
        $document->setValue('billing_zip', $data->billing_zip);

        $document->setValue('campaign_name', $data->campaign_name);

        $document->setValue('pymntTer', $template_document->name);
        $document->setValue('pTerCust', $data->template_document_custom);
        $document->setValue('cr', $data->currency->sign.' ');

        $document->setValue('compCpc', $data->compCpc);
        $document->setValue('compCpa', $data->compCpa);
        $document->setValue('compCpm', $data->compCpm);
        $document->setValue('compCpd', $data->compCpd);
        $document->setValue('compCpi', $data->compCpi);
        $document->setValue('compCps', $data->compCps);
        $document->setValue('compCpl', $data->compCpl);

        $document->setValue('prePay', $data->prepay ? "yes" : "no");
        if($data->prepay) {
            $document->setValue('preAmnt', $data->prepay_amount);
        } else {
            $document->setValue('preAmnt', '');
        }

        $document->setValue('capAmnt', '');
        $document->setValue('capType', '');

        if($data->traffic_search &&
        $data->traffic_banner &&
        $data->traffic_popup &&
        $data->traffic_context &&
        $data->traffic_exit &&
        $data->traffic_email &&
        $data->traffic_path &&
        $data->traffic_social &&
        $data->traffic_mobile){
            $traffic_all = "X";
        } else {
            $traffic_all = "";
        }
        $data->traffic_all = $traffic_all;

        $arrTraffic = [
            1 => ["field" => "traffic_search", "label" => "X Search"],
            2 => ["field" => "traffic_banner", "label" => "X Banners"],
            3 => ["field" => "traffic_popup", "label" => "X Pop-Ups"],
            4 => ["field" => "traffic_context", "label" => "X Contextual"],
            5 => ["field" => "traffic_exit", "label" => "X Exit"],
            6 => ["field" => "traffic_email", "label" => "X Email"],
            7 => ["field" => "traffic_path", "label" => "X Path"],
            8 => ["field" => "traffic_social", "label" => "X Social"],
            9 => ["field" => "traffic_mobile", "label" => "X Mobile"],
            10 => ["field" => "traffic_all", "label" => "X All"],
        ];

        $count = 1;
        foreach ($arrTraffic as $key => $value) {
            if($data->{$value['field']}){
                $document->setValue("traffic_$count", $value['label']);
                $count ++;
            }
        }
        for($count; $count < 11; $count ++){
            $document->setValue("traffic_$count", '');
        }
        unset($count);
        unset($data->traffic_all);

//        $document->setValue('search', $data->traffic_search ? "X" : "");
//        $document->setValue('banner', $data->traffic_banner ? "X" : "");
//        $document->setValue('popup', $data->traffic_popup ? "X" : "");
//        $document->setValue('context', $data->traffic_context ? "X" : "");
//        $document->setValue('exit', $data->traffic_exit ? "X" : "");
//        $document->setValue('trEmail', $data->traffic_email ? "X" : "");
//        $document->setValue('path', $data->traffic_path ? "X" : "");
//        $document->setValue('social', $data->traffic_social ? "X" : "");
//        $document->setValue('trMobile', $data->traffic_mobile ? "X" : "");
//        $document->setValue('trAll', $traffic_all);

        $document->setValue('noAdult', $data->restricted_no_adult ? "No Adult" : "");
        $document->setValue('noIncent', $data->restricted_no_incent ? "No Incent" : "");
        $document->setValue('noRebrokering', $data->restricted_no_rebrokering ? "No Rebrokering" : "");
        $document->setValue('noAffiliateNetwork', $data->restricted_no_affiliate_net ? "No Affiliate Network" : "");
        $document->setValue('None', $data->restricted_none ? "None" : "");

        $count = ['small' => 1, 'big' => 1];
        foreach($dataTrafficPpc as $iter){
            if(isset($traffic_ppc[$iter->id])){
                if(strlen($iter->name) > 11){
                    var_dump("tf_ppc_long_" . $count['big']);
                    $document->setValue("tf_ppc_long_" . $count['big'], "X " . $iter->name);
                    $count['big'] ++;
                } else {
                    var_dump("tf_ppc_" . $count['small']);
                    $document->setValue("tf_ppc_" . $count['small'], "X " . $iter->name);
                    $count['small'] ++;
                }
//                $document->setValue($iter->place_holder, "X");
            } else {
//                $document->setValue($iter->place_holder, "");
            }
        }
        for($count['small']; $count['small'] < 9; $count['small'] ++){
            $document->setValue("tf_ppc_" . $count['small'], "");
        }
        for($count['big']; $count['big'] < 5; $count['big'] ++){
            $document->setValue("tf_ppc_long_" . $count['big'], "");
        }

        $note = htmlspecialchars($data->note);

        $document->setValue('ionotes', $note);
        $document->setValue('mobAttrPlatform', $data->mobile_attribut_platform);
        $document->setValue('governing', $data->governing_term);

    }


    public function converPDF(IO $data)
    {
        Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));
        Settings::setPdfRendererName('TCPDF');

        //Load temp file
        $phpWord = \PhpOffice\PhpWord\IOFactory::load(public_path($data->path_docx . $data->google_file_name . ".docx"));

        //Save it
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord , 'PDF');

        $xmlWriter->save(public_path($data->path_pdf . $data->google_file_name . ".pdf"));
    }


//    public function createCreditDocx(IO $data)
//    {
//        $file = public_path('/io/docx/template/Credit_Application_Template.docx');
//
//        if(file_exists($file) == false){
//            throw new Exception('Error: Template credit file not exist.');
//        }
//
//        $document = new \PhpOffice\PhpWord\TemplateProcessor($file);
//
//        $document->setValue("COMPANY_CONTACT", $data->billing_contact);
//        $document->setValue("%COMPANY%", $data);
//        $document->setValue("PADDRESS", $data->billing_street1);
//        $document->setValue("PADDRESS2", $data->billing_street2);
//        $document->setValue("PCITY", $data->billing_city);
//        $document->setValue("PSTATE", $data->billing_state_param->name);
//        $document->setValue("PZIP", $data->billing_zip);
//        $document->setValue("%CPHONE%", $data);
//        $document->setValue("%CEMAIL%", $data);
//        $document->setValue("%CADDRESS%", $data);
//        $document->setValue("%CADDRESS2%", $data);
//        $document->setValue("%CCITY%", $data);
//        $document->setValue("%CSTATE%", $data);
//        $document->setValue("%CZIP%", $data);
//        $document->setValue("%PCONTACT%", $data);
//
//        $document->saveAs(public_path($data->path_credit_docx . $data->google_file_name . ".docx"));
//    }
//
//
//    public function converCreditPDF(IO $data)
//    {
//        Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));
//        Settings::setPdfRendererName('TCPDF');
//
//        //Load temp file
//        $phpWord = \PhpOffice\PhpWord\IOFactory::load(public_path($data->path_credit_docx . $data->google_file_name . ".docx"));
//
//        //Save it
//        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord , 'PDF');
//
//        $xmlWriter->save(public_path($data->path_credit_pdf . $data->google_file_name . ".pdf"));
//    }

}