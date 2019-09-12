<?php

namespace App\Http\Controllers\Admin;

use App\Console\Commands\Cron\StatRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Advertiser;
use App\Models\AdvertiserStat;

use Exception;
use DateTime;
use DateTimeZone;
use Carbon\Carbon;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception AS ExceptionPHPMailer;

use App\Services\Google;
use App\Services\GoogleDrive;

use App\Services\EverFlow\Advertiser as EF_Advertiser;
use App\Services\EverFlow\Affiliate as EF_Affiliate;
use App\Services\EverFlow\Campaign as EF_Campaign;
use App\Services\EverFlow\Offer as EF_Offer;
use App\Services\EverFlow\General as EF_General;
use App\Services\LinkTrust\Advertiser as LT_Advertiser;
use App\Services\LinkTrust\Affiliate as LT_Affiliate;
use App\Services\LinkTrust\Offer as LT_Offer;
use App\Services\QB\Core as QB_Core;

use App\Services\PhpWord;
use App\Services\Mailer;

use App\Services\PipeDrive\General as PP_General;
use App\Services\PipeDrive\Deal as PP_Deal;
use App\Services\PipeDrive\Organization as PP_Organization;
use App\Services\PipeDrive\Person as PP_Person;

use PhpOffice\PhpWord\Settings;

use App\Models\Advertiser as modelAdvertiser;
use App\Models\IO as modelIO;
use App\Models\Offer as modelOffer;
use App\Models\OfferCreative as modelCreative;
use App\Models\IODocusignPosition as modelIODocusignPosition;
use App\Models\PipeDrive\Deal as modelPipeDriveDeal;
use App\Models\Request\Price as modelRequestPrice;
use App\Models\CreditCap as modelCreditCap;

use DB;
use App\Models\QB\Access as modelQBAccess;

use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Utility\Configuration\ConfigurationManager;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Purchase;
use QuickBooksOnline\API\Data\IPPPurchase;
use QuickBooksOnline\API\QueryFilter\QueryMessage;
use QuickBooksOnline\API\ReportService\ReportService;
use QuickBooksOnline\API\ReportService\ReportName;

//use Session;


class TestController extends Controller
{
    public function __construct()
    {
        //dd(session()->get('page_access_auth'));
    }

    public function index(Request $request)
    {

        exit();

        //$data = modelIO::find(1173);
        $data = modelIO::find(1128);



        $mailer = new Mailer();
        $result = $mailer->sendNewIO($data);

        dd($result);

//        $result = $data->createGoogleDriveDocx();
//
//        if($result){
//            $data->save();
//        }
//
//        dd($result);


//        $head = 'Q03FpY3J3pVgGTjvks1xEAOKLWArVG2vXV2eOQwJaHQ=';
//        $token = '20179fff-f40b-45e5-a431-354680e7c320';
//
//        $str2 = base64_decode($head);
//        $str2 = bin2hex($str2);
//
//        $str = hash_hmac('sha256', $head, $token, false);
//
//        var_dump($str2);
//        dd($str);



//        $str = '{
//"eventNotifications":[{
//"realmId":"193514836701984",
//"dataChangeEvent":{
//	"entities":[{
//		"name":"Invoice",
//		"id":"75",
//		"operation":"Create",
//		"lastUpdated":"2018-12-07T20:01:13.000Z"
//	},{
//		"name":"Invoice",
//		"id":"75",
//		"operation":"Delete",
//		"lastUpdated":"2018-12-07T20:05:24.538Z"
//	}]
//}
//}]
//}';
//
//        $param = json_decode($str);
//
//        dd($param);


        $qbCore = new QB_Core();

        $dataService = $qbCore->getDataService();
        $serviceContext = $dataService->getServiceContext();

//        $sql = "SELECT * FROM Invoice WHERE MetaData.CreateTime >= '2018-10-22T04:05:05-07:00' AND MetaData.CreateTime <= '2018-10-24T04:05:05-07:00'";
//        $sql = "SELECT * FROM Invoice WHERE TotalAmt > '1000.0'";
//        $sql = "SELECT * FROM Invoice";
//        $sql = "SELECT * FROM Payment";

//        $data = $dataService->Query($sql);
        $data = $dataService->FindbyId('invoice', 63);
//        $data = $dataService->FindbyId('payment', 128);



//        dd($invoice);
        dd($data);

//        $reportService = new ReportService($serviceContext);
//        if (!$reportService) {
//            exit("Problem while initializing ReportService.\n");
//        }
//
//        $reportService->setStartDate("2015-01-01");
//        $reportService->setAccountingMethod("Accrual");
//
//        $profitAndLossReport = $reportService->executeReport(ReportName::PROFITANDLOSS);
//
//        dd($profitAndLossReport);

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

//        $authorizationCodeUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
//
//        dd($authorizationCodeUrl);

        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken("L0115402195780IXgZfJrm3UhrxLJPf3KZeizg276Mte3Qo9uM", "123146156988829");

        dd($accessToken);

        exit();

        return view('admin.test.index');
    }


    public function word(Request $request)
    {


        //$request->session()->put('test_123', 'value');
        //session()->save();

        //dd($request->session()->get('test_123'));




        exit();


        $mailer = new Mailer();
        $phpWord = new PhpWord();

        $data = modelIO::find(1123);
        //$data->createGoogleDriveDocx();

        $result = $mailer->sendNewIO($data);
dd($result);
        exit();

        dd(preg_replace ("/[^a-zA-ZА-Яа-я0-9\s]/","", $data->company_contact));



        $phpWord = new PhpWord();
        $phpWord->createDocx($data);
        //$phpWord->createDocx($data);

        //$mailer->sendNewIO($data);
        //$phpWord->createDocx($data);
        //$phpWord->converPDF($data);
    }


    public function delete()
    {
        //User::where('id', '>', 3)->delete();
    }


    public function mail()
    {
        exit();

        /*$mail = new PHPMailer(true);                    // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = config('mail.host');                    // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = config('mail.username');            // SMTP username
            $mail->Password = config('mail.password');            // SMTP password
            $mail->SMTPSecure = config('mail.encryption');        // Enable TLS encryption, `ssl` also accepted
            $mail->Port = config('mail.port');                    // TCP port to connect to


            //Recipients
            $mail->setFrom('max.work23@gmail.com', 'Max Mailer');
            $mail->addAddress('max@seccosquared.com', 'Max Secco');     // Add a recipient
//            $mail->addAddress('ellen@example.com');               // Name is optional
//            $mail->addReplyTo('info@example.com', 'Information');
//            $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');

//            //Attachments
//            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
//            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (ExceptionPHPMailer $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }*/

        $google = new \App\Services\Google();
        $google = new \App\Services\Google();
        $google->checkAccessToken();

        $client = $google->getClient();
        dd($client->getAccessToken());
        $client->setSubject('max@seccosquared.com');



        //$client->$client->fetchAccessTokenWithAuthCode($code);


        $service = new \Google_Service_Gmail($client);
        $mailer = $service->users_messages;

        //$result = $mailer->listUsersMessages('max@seccosquared.com');

        //dd($result);

        $message = (new \Swift_Message('Here is my subject'))
            ->setFrom('max@seccosquared.com')
            ->setTo('connect7@mail.ua')
            //->setTo([/*'onlysendemail777@gmail.com'*/'max.work23@gmail.com'])
            ->setContentType('text/html')
            ->setCharset('utf-8')
            ->setBody('<h4>Test Gmail Api</h4>');

        $msg_base64 = (new \Swift_Mime_ContentEncoder_Base64ContentEncoder())
            ->encodeString($message->toString());

        $message = new \Google_Service_Gmail_Message();
        $message->setRaw($msg_base64);

        $result = $mailer->send('me', $message);

        dd($result);
    }

    public function lt()
    {
exit();
//        $dataAdvertiser = Advertiser::find(1);
//
//        $data = array(
//            "AutoApproveApplication" => 'True',
//            "SuccessUrl" => "http://admin.seccosquared.com",
//            "FailureUrl" => "http://admin.seccosquared.com",
//            "ContactName" => $dataAdvertiser->name,
//            "CompanyName" => $dataAdvertiser->contact,
//            "ContactEmail" => $dataAdvertiser->email,
//            "AddressLine1" => $dataAdvertiser->street1,
//            "AddressLine2" => $dataAdvertiser->street2,
//            "Province" => $dataAdvertiser->province,
//            "PostalCode" => $dataAdvertiser->zip,
//            "Country" => $dataAdvertiser->country,
//            "City" => $dataAdvertiser->city,
//            "State" => $dataAdvertiser->state,
//            "Phone" => $dataAdvertiser->phone
//        );
//
//        try {
//            $ch = curl_init();
//
//            curl_setopt($ch, CURLOPT_URL, "http://merchant.seccosquared.com/Signup/Custom");
//            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//            curl_setopt($ch, CURLOPT_POST, count($data));
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//
//            $resp = curl_exec($ch);
//
//            var_dump(curl_getinfo($ch));
//
//            curl_close($ch);
//
//        } catch (Exception $e) {
//
//            throw new Exception('Error: Curl failed with error ' . $e->getMessage());
//        }
//
//        echo $resp;

//        $startDate = date('n/j/Y', strtotime('2018-07-19'));
//        $endDate = date('n/j/Y', strtotime('2018-07-19'));
//        $startDate = date('n/j/Y', strtotime('2018-06-29'));
//        $endDate = date('n/j/Y', strtotime('2018-06-29'));

//        $lt = new LT_Advertiser();
//        $data = $lt->getStat($startDate, $endDate);

//        $lt_Affiliate = new LT_Affiliate();
//        $data = $lt_Affiliate->getStat($startDate, $endDate);
//
//        var_dump($startDate);
//
//        dd($data);
//        dd($data->Affiliate[24]);

//        $data = [
//            "AutoApproveApplication"=> "True",
//            "SuccessUrl" => "http://admin.seccosquared.com",
//            "FailureUrl" => "http://admin.seccosquared.com",
//            "ContactName" => "test company name by max",
//            "CompanyName" => "test advertiser contact name by max",
//            "ContactEmail" => "max@seccosquared.com",
//            "AddressLine1" => "address 1",
//            "AddressLine2" => "address 2",
//            "Province" => NULL,
//            "PostalCode" => "76000",
//            "Country" => "US",
//            "City" => "New York",
//            "State" => "NY",
//            "Phone" => "1234567890"];
//
//        $ch = curl_init();
//
//        curl_setopt($ch, CURLOPT_URL, "http://merchant.seccosquared.com/Signup/Custom");
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_POST, count($data));
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//
//        $response = curl_exec($ch);
//
//        dd($response);

        dd(trim("   "));

        $dateStart = "08/20/2018";
        $dateEnd = "08/20/2018";

        $lt_Affiliate = new LT_Affiliate();
        $lt_Offer = new LT_Offer();

        $result = $lt_Offer->getStat($dateStart, $dateEnd);
        //$result = $lt_Affiliate->getStat($dateStart, $dateEnd);
        //$result = $lt_Affiliate->getAllAffiliate();

        dd($result->Campaign[5]);

    }

    public function ef()
    {
        exit();
//        $model = new Advertiser();
//        $data = $model->find(1867);

//        dd(json_encode($param));

//        $everFlow = new EverFlow();
//        $res = $everFlow->createAdvertiser($data);
//
//        dd($res);

//        $var = '{"network_advertiser_id":128157,"network_id":66,"name":"name for test","account_status":"pending","network_employee_id":23,"internal_notes":"","address_id":2087,"is_contact_address_enabled":false,"sales_manager_id":0,"is_expose_publisher_reporting_data":false,"time_created":1527103374,"time_saved":1527103374,"relationship":{"labels":{"total":0,"entries":[]},"users":{"total":0,"entries":[]},"account_manager":{"first_name":"Max","last_name":"Loonkeo","email":"max@seccosquared.com","work_phone":"","cell_phone":"","instant_messaging_id":0,"instant_messaging_identifier":""},"contact_address":{"address_1":"address 1","address_2":"address 2","city":"city","region_code":"00","country_id":227,"country_code":"US","zip_postal_code":"76000"}}}';
//        $arr = json_decode($var);
//
//        dd($arr);

//        $start = "2018-01-01";
//        $end = "2018-01-03";
//
//        $dateStart = new DateTime($start, new DateTimeZone("America/New_York"));
//        $dateEnd = new DateTime($end, new DateTimeZone("America/New_York"));
//
//        while($dateStart <= $dateEnd){
//
//            var_dump($dateStart->format("Y-m-d"));
//
//            $dateStart->modify('+1 day');
//
//        }
//
//        var_dump($dateStart);
//        var_dump($dateEnd);

//        exit();

//
//        $EF_Advertiser = new EF_Advertiser();
//
//        $efStat = $EF_Advertiser->getStat($dateStart->format('Y-m-d'), $dateEnd->format('Y-m-d'));
//
//        dd($efStat->table[24]);

//        $class = new EF_Advertiser();
//        $class->updateAdvertiser($data);
//
//        $data = $class->getAdvertiser(124541);
//
//        dd($data);

//        $data = modelOffer::where('ef_id', 25)->first();
//        $data->updateEFUrl();
//
//        exit();

        $ef_Affiliate = new EF_Affiliate();
        $ef_Campaign = new EF_Campaign();
        $ef_Offer = new EF_Offer();
        $ef_General = new EF_General();
        //$ef_Affiliate->getAffiliate(202847);

        //$result = $ef_Affiliate->getAllAffiliate();
        //$data = $ef_Campaign->getAllCampaign();
        //$ef_Offer->getOffer(272597);
        //$data = $ef_Offer->getAllOffer();

        $data = $ef_Offer->getTrackingAffiliate(25, 115, 0, 0);

        dd($data);

//        $dataPrice = modelRequestPrice::find(861);
//
//        $ef_Offer->updateOfferPrice($dataPrice);
//
//        $ef_Offer->getPayoutRevenue();
//
//        $data = $ef_Offer->getOffer(414971);
//        dd($data->relationship);
//        exit();

//        $data = $ef_General->getAllTimezone();
//
//        foreach($data as $iter){
//            var_dump($iter->timezone);
//            var_dump($iter->timezone_id);
//        }


//        $domain = $ef_General->getAllDomain();
//        dd($domain);

//        $ef_Offer->getTrackingAffiliate(26, 0, 97);
        $ef_Offer->getAllVisibleOffer();


//        $result = $ef_Offer->getStat("2018-08-17", "2018-08-18");
        $result = $ef_Affiliate->getStatByAffiliate("2018-07-01", "2018-07-31");
//        dd($result->table[0]);

//        $result = $ef_Offer->getOffer(25);

        $tmp = [];
        foreach($result->table as $iter){

            $tmp[$iter->columns[0]->id][] = $iter->columns[1]->id;

        }

        dd($tmp);

        dd($result->table[1]);
        dd($result->affiliates[9]);

    }

    public function pipe()
    {
        exit();
        $pipeGeneral = new PP_General();
        $pipeDeal = new PP_Deal();
        $pipeOrganization = new PP_Organization();
        $pipePerson = new PP_Person();

        //$pipeGeneral->getMe();
        //$pipeGeneral->getAllUser();
        //$pipeGeneral->getAllRole();
        //$pipeDeal->getAll();
        //$pipeDeal->getByID(685);

        //var_dump('organization');
        //$pipeOrganization->getByID(8187);

        //var_dump('person');
        //$pipePerson->getByID(10589);

        //var_dump('organization 2');
        //$pipeOrganization->getByID(4985);

        //var_dump('organization 3');
        //$pipeOrganization->getByID(4249);

        //var_dump('organization 4');
        //$pipeOrganization->getByID(4895);

        //$pipeOrganization->getAll();

        $data = modelPipeDriveDeal::find(2);

        $param = json_decode($data->request_body);

        var_dump('deal webhooks');
        dd($param->current);
    }

    public function docusign()
    {
        exit();
/*
        $documentCreditFileName = public_path("io/pdf/template/" . "Credit_Application_Template" . ".pdf");
        if(!file_exists($documentCreditFileName)){
            throw new Exception('Error: PDF file for Docusign not exist.');
        }

        $dataIO = modelIO::find(1098);

//        $word = new PhpWord();
//
//        $word->converPDF($dataIO);
//        $dataIO = modelIO::find(1094);

        $docusign = new \App\Services\Docusign\Core();

        $result = $docusign->loadDocumentTest($dataIO, 'created');

        dd($result);*/

        $data = modelIO::find(1123);

        $data->

        $docusign = new \App\Services\Docusign\Core();

        $envelopeId = $docusign->loadDocument($data, "created");

        dd($envelopeId);
    }


    public function docusignDoc()
    {
        exit();
        $data = modelIO::find(1144);

        $docusign =  new \App\Services\Docusign\Core();

        $file = $docusign->downloadDocument($data);

        dd($file);
    }


    public function docusignInfo()
    {
        exit();
        $data = modelIO::find(1123);

        $docusign =  new \App\Services\Docusign\Core();

        $info = $docusign->getDocumentInfo($data);

        dd($info);
    }



    public function pdf()
    {
exit();
        Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));
        Settings::setPdfRendererName('TCPDF');

        //dd(file_exists("/var/www/public/io/133163.docx"));

        //Load temp file
        $phpWord = \PhpOffice\PhpWord\IOFactory::load("/var/www/public/io/docx/result/133163.docx");

        //Save it
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord , 'PDF');

        $xmlWriter->save("/var/www/public/io/pdf/result/133163_new.pdf");

        exit();


        $dataIO = modelIO::find(1094);

        $res = copy("/tmp/7v2miU" , public_path($dataIO->path_docusign . $dataIO->google_file_name . ".pdf"));

        dd($res);



        $username = "max@seccosquared.com";
        $password = "connect123NEW";
        $integratorKey = "00620924-9de1-4049-9bbf-57bfdbf78112";

        // change to production (www.docusign.net) before going live
        $host = "https://demo.docusign.net/restapi";

        //$testConfig = new TestConfig($username, $password, $integratorKey, $host);

        $config = new \DocuSign\eSign\Configuration();
        $config->setHost($host);
        $config->addDefaultHeader("X-DocuSign-Authentication", "{\"Username\":\"" . $username . "\",\"Password\":\"" . $password . "\",\"IntegratorKey\":\"" . $integratorKey . "\"}");

        $ApiClient = new \DocuSign\eSign\ApiClient($config);

        $authenticationApi = new \DocuSign\eSign\Api\AuthenticationApi($ApiClient);

        $options = new \DocuSign\eSign\Api\AuthenticationApi\LoginOptions();
        $loginInformation = $authenticationApi->login($options);

        if ($loginInformation && count((array)$loginInformation) > 0) {

            $loginAccount = $loginInformation->getLoginAccounts()[0];
            if (isset($loginInformation)) {

                $AccountId = $loginAccount->getAccountId();

            }
        }

        $EnvelopeId = "5d798bae-481c-42c5-a6e2-c45db694d8e4";

        $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($ApiClient);
        $docsList = $envelopeApi->listDocuments($AccountId, $EnvelopeId);

        /*$this->assertNotEmpty($docsList);
        $this->assertNotEmpty($docsList->getEnvelopeId());*/

        $docCount = count($docsList->getEnvelopeDocuments());
        if (intval($docCount) > 0)
        {
            //dd($docsList->getEnvelopeDocuments());

            foreach($docsList->getEnvelopeDocuments() as $document)
            {
                //dd($document);

                var_dump($document->getDocumentId());

                if($document->getDocumentId()){

                    $DocumentId = "1";

                    $file = $envelopeApi->getDocument($AccountId, $DocumentId, $EnvelopeId);


                    //$file = $envelopeApi->getDocument($testConfig->getAccountId(), $testConfig->getEnvelopeId(), $document->getDocumentId());

                    dd($file);

                    //$this->assertNotEmpty($file);
                }
            }
        }
    }


    public function createGoogleFolder()
    {
        exit();
        $arr = [
            128214, 128213, 128211, 128207, 128203, 128202, 128168,
            128188, 128181, 128163, 128155, 128154,

            128220, 128205, 128204, 128201, 128200, 128199, 128198, 128196,
            128192, 128191, 128180, 128175, 128174, 128173, 128172, 128171,
            128170, 128169, 128167, 128166, 128165, 128164, 128162, 128161,
            128160,
        ];

        foreach($arr as $key => $ef_id){

            $data = modelAdvertiser::where('ef_id', $ef_id)
                ->whereNull('google_folder')
                ->first();

            if($data){

                $data->createGoogleDriveFolder();
                $data->save();

                var_dump($data->ef_id . " " . $data->google_folder);
            }
        }
    }

}
