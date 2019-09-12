<?php

namespace App\Services\Docusign;

use App\Services\Docusign\Config;

use App\Models\IO;
use App\Models\IODocusignPosition;

use Exception;

class Core
{

    public $config;

    protected $username;
    protected $pass;
    protected $key;
    protected $host;

    public function __construct()
    {
        $this->username = config('services.docusign.username');
        $this->pass = config('services.docusign.password');
        $this->key = config('services.docusign.integratorKey');
        $this->host = config('services.docusign.host');

        $this->setConfig();
        $this->login();
    }


    protected function setConfig()
    {
//        var_dump($this->username);
//        var_dump($this->pass);
//        var_dump($this->key);
//        var_dump($this->host);
//
//        exit();

        $this->config = new Config($this->username, $this->pass, $this->key, $this->host);

        $config = new \DocuSign\eSign\Configuration();
        $config->setHost($this->host);
        $config->addDefaultHeader("X-DocuSign-Authentication", "{\"Username\":\"" . $this->username . "\",\"Password\":\"" . $this->pass . "\",\"IntegratorKey\":\"" . $this->key . "\"}");

        $apiClient = new \DocuSign\eSign\ApiClient($config);

        $this->config->setApiClient($apiClient);
    }


    protected function login()
    {
        $authenticationApi = new \DocuSign\eSign\Api\AuthenticationApi($this->config->getApiClient());

        $options = new \DocuSign\eSign\Api\AuthenticationApi\LoginOptions();
        $loginInformation = $authenticationApi->login($options);
//var_dump(session()->all());
//dd($loginInformation);
        if ($loginInformation && count((array)$loginInformation) > 0) {

            $loginAccount = $loginInformation->getLoginAccounts()[0];

            if ($loginAccount) {

                $accountId = $loginAccount->getAccountId();
                $this->config->setAccountId($accountId);

            } else {
                throw new Exception('Error: Docusign auth faild.');
            }
        } else {
            throw new Exception('Error: Docusign auth faild.');
        }
    }


    public function loadDocument(IO $dataIO, $status = "sent")
    {
        $documentFileName =  public_path($dataIO->path_pdf . $dataIO->google_file_name . ".pdf");
        $documentName = $dataIO->google_file_name . ".docx";

        $dataManager = $dataIO->docusign_manager;

        $templateDocument = $dataIO->getTemplateDocumet();

        $modelPosition = new IODocusignPosition();
        $dataPosition = $modelPosition->getByTemplate($templateDocument->id);

        if(!file_exists($documentFileName)){
            throw new Exception('Error: PDF file for Docusign not exist.');
        }
        if(!$dataPosition){
            throw new Exception('Error: Docusign params for document template not found.');
        }

        if(!empty($this->config->getAccountId()))
        {
            $envelope_events = [
                (new \DocuSign\eSign\Model\EnvelopeEvent())->setEnvelopeEventStatusCode("sent"),
                (new \DocuSign\eSign\Model\EnvelopeEvent())->setEnvelopeEventStatusCode("delivered"),
                (new \DocuSign\eSign\Model\EnvelopeEvent())->setEnvelopeEventStatusCode("completed"),
                (new \DocuSign\eSign\Model\EnvelopeEvent())->setEnvelopeEventStatusCode("declined"),
                (new \DocuSign\eSign\Model\EnvelopeEvent())->setEnvelopeEventStatusCode("voided"),
                (new \DocuSign\eSign\Model\EnvelopeEvent())->setEnvelopeEventStatusCode("sent"),
                (new \DocuSign\eSign\Model\EnvelopeEvent())->setEnvelopeEventStatusCode("sent")
            ];

            /*$recipient_events = [
                (new \DocuSign\eSign\Model\RecipientEvent())->setRecipientEventStatusCode("Sent"),
                (new \DocuSign\eSign\Model\RecipientEvent())->setRecipientEventStatusCode("Delivered"),
                (new \DocuSign\eSign\Model\RecipientEvent())->setRecipientEventStatusCode("Completed"),
                (new \DocuSign\eSign\Model\RecipientEvent())->setRecipientEventStatusCode("Declined"),
                (new \DocuSign\eSign\Model\RecipientEvent())->setRecipientEventStatusCode("AuthenticationFailed"),
                (new \DocuSign\eSign\Model\RecipientEvent())->setRecipientEventStatusCode("AutoResponded")
            ];*/

            $event_notification = new \DocuSign\eSign\Model\EventNotification();
            $event_notification->setUrl(route('api.io.listen.api.docusign') . '?op=webhook');
            $event_notification->setLoggingEnabled("true");
            $event_notification->setRequireAcknowledgment("true");
            $event_notification->setUseSoapInterface("false");
            $event_notification->setIncludeCertificateWithSoap("false");
            $event_notification->setSignMessageWithX509Cert("false");
            $event_notification->setIncludeDocuments("false"); /*true*/
            $event_notification->setIncludeEnvelopeVoidReason("true");
            $event_notification->setIncludeTimeZone("true");
            $event_notification->setIncludeSenderAccountAsCustomField("false"); /*true*/
            $event_notification->setIncludeDocumentFields("false"); /*true*/
            $event_notification->setIncludeCertificateOfCompletion("false"); /*true*/
            $event_notification->setEnvelopeEvents($envelope_events);
            /*$event_notification->setRecipientEvents($recipient_events);*/

            $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($this->config->getApiClient());
            // Add a document to the envelope

            /*$documentArray = [];
            if($dataIO->credit) {
                $documentCreditFileName = public_path("io/pdf/template/" . "Credit_Application_Template" . ".pdf");
                $documentNameCredit = "Credit_Application_Template.pdf";
                $documentIdCredit = 2;

                if(!file_exists($documentCreditFileName)){
                    throw new Exception('Error: PDF Credit file for Docusign not exist.');
                }

                $documentCredit = new \DocuSign\eSign\Model\Document();
                $documentCredit->setDocumentBase64(base64_encode(file_get_contents($documentCreditFileName)));
                $documentCredit->setName($documentNameCredit);
                $documentCredit->setDocumentId($documentIdCredit);

                $documentArray[] = $documentCredit;
            }*/

            $document = new \DocuSign\eSign\Model\Document();
            $document->setDocumentBase64(base64_encode(file_get_contents($documentFileName)));
            $document->setName($documentName);
            $document->setDocumentId($dataIO->order_number);

            $documentArray[] = $document;

            // Create a |SignHere| tab somewhere on the document for the recipient to sign

            $signHere_1 = new \DocuSign\eSign\Model\SignHere();
            $signHere_1->setDocumentId($dataIO->order_number);
            $signHere_1->setRecipientId("1");
            $signHere_1->setAnchorString($dataPosition['sign_here']->secco_string);
            $signHere_1->setAnchorIgnoreIfNotPresent(false);
            $signHere_1->setAnchorUnits($dataPosition['sign_here']->secco_units);
            $signHere_1->setAnchorXOffset($dataPosition['sign_here']->secco_x_offset);
            $signHere_1->setAnchorYOffset($dataPosition['sign_here']->secco_y_offset);

            $fullName_1 = new \DocuSign\eSign\Model\FullName();
            $fullName_1->setDocumentId($dataIO->order_number);
            $fullName_1->setRecipientId("1");
            $fullName_1->setAnchorString($dataPosition['full_name']->secco_string);
            $fullName_1->setAnchorIgnoreIfNotPresent(false);
            $fullName_1->setAnchorUnits($dataPosition['full_name']->secco_units);
            $fullName_1->setAnchorXOffset($dataPosition['full_name']->secco_x_offset);
            $fullName_1->setAnchorYOffset($dataPosition['full_name']->secco_y_offset);

            $signTitle_1 = new \DocuSign\eSign\Model\Title();
            $signTitle_1->setDocumentId($dataIO->order_number);
            $signTitle_1->setRecipientId("1");
            $signTitle_1->setAnchorString($dataPosition['title']->secco_string);
            $signTitle_1->setAnchorIgnoreIfNotPresent(false);
            $signTitle_1->setAnchorUnits($dataPosition['title']->secco_units);
            $signTitle_1->setAnchorXOffset($dataPosition['title']->secco_x_offset);
            $signTitle_1->setAnchorYOffset($dataPosition['title']->secco_y_offset);

            $signDate_1 = new \DocuSign\eSign\Model\DateSigned();
            $signDate_1->setDocumentId($dataIO->order_number);
            $signDate_1->setRecipientId("1");
            $signDate_1->setAnchorString($dataPosition['date_signed']->secco_string);
            $signDate_1->setAnchorIgnoreIfNotPresent(false);
            $signDate_1->setAnchorUnits($dataPosition['date_signed']->secco_units);
            $signDate_1->setAnchorXOffset($dataPosition['date_signed']->secco_x_offset);
            $signDate_1->setAnchorYOffset($dataPosition['date_signed']->secco_y_offset);


            $signHere_2 = new \DocuSign\eSign\Model\SignHere();
            $signHere_2->setDocumentId($dataIO->order_number);
            $signHere_2->setRecipientId("2");
            $signHere_2->setAnchorString($dataPosition['sign_here']->client_string);
            $signHere_2->setAnchorIgnoreIfNotPresent(false);
            $signHere_2->setAnchorUnits($dataPosition['sign_here']->client_units);
            $signHere_2->setAnchorXOffset($dataPosition['sign_here']->client_x_offset);
            $signHere_2->setAnchorYOffset($dataPosition['sign_here']->client_y_offset);

            $fullName_2 = new \DocuSign\eSign\Model\FullName();
            $fullName_2->setDocumentId($dataIO->order_number);
            $fullName_2->setRecipientId("2");
            $fullName_2->setAnchorString($dataPosition['full_name']->client_string);
            $fullName_2->setAnchorIgnoreIfNotPresent(false);
            $fullName_2->setAnchorUnits($dataPosition['full_name']->client_units);
            $fullName_2->setAnchorXOffset($dataPosition['full_name']->client_x_offset);
            $fullName_2->setAnchorYOffset($dataPosition['full_name']->client_y_offset);

            $signTitle_2 = new \DocuSign\eSign\Model\Title();
            $signTitle_2->setDocumentId($dataIO->order_number);
            $signTitle_2->setRecipientId("2");
            $signTitle_2->setAnchorString($dataPosition['title']->client_string);
            $signTitle_2->setAnchorIgnoreIfNotPresent(false);
            $signTitle_2->setAnchorUnits($dataPosition['title']->client_units);
            $signTitle_2->setAnchorXOffset($dataPosition['title']->client_x_offset);
            $signTitle_2->setAnchorYOffset($dataPosition['title']->client_y_offset);

            $signDate_2 = new \DocuSign\eSign\Model\DateSigned();
            $signDate_2->setDocumentId($dataIO->order_number);
            $signDate_2->setRecipientId("2");
            $signDate_2->setAnchorString($dataPosition['date_signed']->client_string);
            $signDate_2->setAnchorIgnoreIfNotPresent(false);
            $signDate_2->setAnchorUnits($dataPosition['date_signed']->client_units);
            $signDate_2->setAnchorXOffset($dataPosition['date_signed']->client_x_offset);
            $signDate_2->setAnchorYOffset($dataPosition['date_signed']->client_y_offset);


            $tabs_1 = new \DocuSign\eSign\Model\Tabs();
            $tabs_1->setSignHereTabs(array($signHere_1));
            $tabs_1->setFullNameTabs(array($fullName_1));
            $tabs_1->setTitleTabs(array($signTitle_1));
            $tabs_1->setDateSignedTabs(array($signDate_1));

            $tabs_2 = new \DocuSign\eSign\Model\Tabs();
            $tabs_2->setSignHereTabs(array($signHere_2));
            $tabs_2->setFullNameTabs(array($fullName_2));
            $tabs_2->setTitleTabs(array($signTitle_2));
            $tabs_2->setDateSignedTabs(array($signDate_2));


            $signer_1 = new \DocuSign\eSign\Model\Signer();
            $signer_1->setEmail($dataManager->email);
            $signer_1->setName($dataManager->name);
            $signer_1->setRecipientId("1");

            $signer_2 = new \DocuSign\eSign\Model\Signer();
            $signer_2->setEmail($dataIO->docusign_email_advertiser);
            $signer_2->setName($dataIO->docusign_name_advertiser);
            $signer_2->setRecipientId("2");

            /*if($embeddedSigning) {
                $signer->setClientUserId($testConfig->getClientUserId());
            }*/

            $signer_1->setTabs($tabs_1);
            $signer_2->setTabs($tabs_2);
            // Add a recipient to sign the document
            $recipients = new \DocuSign\eSign\Model\Recipients();
            $recipients->setSigners(array($signer_1, $signer_2));
            $envelop_definition = new \DocuSign\eSign\Model\EnvelopeDefinition();
            $envelop_definition->setEmailSubject("[$dataIO->company_name] - Please sign this doc");
            // set envelope status to "sent" to immediately send the signature request
            $envelop_definition->setStatus($status);
            $envelop_definition->setRecipients($recipients);
            $envelop_definition->setDocuments($documentArray);
            $envelop_definition->setEventNotification($event_notification);
            $options = new \DocuSign\eSign\Api\EnvelopesApi\CreateEnvelopeOptions();
            $options->setCdseMode(null);
            $options->setMergeRolesOnDraft(null);

            $envelop_summary = $envelopeApi->createEnvelope($this->config->getAccountId(), $envelop_definition, $options);

            if(!empty($envelop_summary))
            {
                if($status == "created")
                {
                    $envelopeId = $envelop_summary->getEnvelopeId();
                }
                else
                {
                    $envelopeId = $envelop_summary->getEnvelopeId();
                }
            }

        } else {
            throw new Exception('Error: Docusign accountId is emapty');
        }

        if(isset($envelopeId) && $envelopeId){
             return $envelopeId;
        } else {
            throw new Exception('Error: Docusign api return error, please try again');
        }

    }


    public function getDocumentInfo(IO $dataIO, $onlyStatus = true)
    {
        if (!empty($this->config->getAccountId())) {

            $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($this->config->getApiClient());
            $options = new \DocuSign\eSign\Api\EnvelopesApi\GetEnvelopeOptions();
            $options->setInclude(null);

            $envelopeId = $dataIO->docusign_id;

            try {

                $envelope = $envelopeApi->getEnvelope($this->config->getAccountId(), $envelopeId, $options);

            } catch (\DocuSign\eSign\ApiException $ex){
                return "Exception Docusign: " . $ex->getMessage();
            }

            if($envelope){
                if($onlyStatus){
                    return $envelope['status'];
                } else {
                    return $envelope;
                }
            } else {
                throw new Exception('Error: Docusign api return error, please try again');
            }

        } else {
            throw new Exception('Error: Docusign accountId is emapty');
        }
    }


    public function downloadDocument(IO $dataIO)
    {
        $accountId = $this->config->getAccountId();
        if(!empty($accountId)) {

            $envelopeId = $dataIO->docusign_id;
            $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($this->config->getApiClient());

            $docsList = $envelopeApi->listDocuments($accountId, $envelopeId);
            $docsCount = count($docsList->getEnvelopeDocuments());

            if(intval($docsCount) > 0){

                foreach($docsList->getEnvelopeDocuments() as $document){

                    $documentId = $document->getDocumentId();
                    if($documentId && intval($documentId)){

                        $file = $envelopeApi->getDocument($accountId, $documentId, $envelopeId);
                        if($file && $file->getRealPath()){

                            $filePath = $file->getRealPath();

                            $result = copy($filePath, public_path($dataIO->path_docusign . $dataIO->google_file_name . ".pdf"));
                            @unlink($filePath);
                        }
                    }
                }
            }

            if(isset($result) && $result){
                return $dataIO->google_file_name;
            } else {
                throw new Exception('Error: Docusign api return error, please try again');
            }

        } else {
            throw new Exception('Error: Docusign accountId is emapty');
        }
    }

}