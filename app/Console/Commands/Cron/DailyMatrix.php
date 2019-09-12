<?php
namespace App\Console\Commands\Cron;

use Illuminate\Console\Command;

use App\Services\Mailer as Mailer;

use App\Models\User as modelUser;
use App\Models\Request\Status as modelStatus;
use App\Models\Request\Creative as modelCreative;
use App\Models\Request\Price as modelPrice;
use App\Models\Offer as modelOffer;

use Validator;
use DB;

use DateTime;
use DateTimeZone;
use Exception;
use PDOException;
use PHPMailer\PHPMailer\Exception AS ExceptionPHPMailer;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class DailyMatrix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/daily:matrix {type} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily matrix report';

    protected $fileName;
    protected $googleFolder;
    protected $titleSubject;
    protected $titleBody;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');

        switch ($type) {
            case "yesterday" :
                $this->yesterday();
                break;
            case "monthly" :
                $this->monthly();
                break;
            case "year" :
                $this->year();
                break;
            case "test" :
                $this->test();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function yesterday()
    {
        $date = $this->argument('date');
        if(!$date){
            $date = date('Y-m-d', strtotime('-1 day'));
        } else {
            if(DateTime::createFromFormat('Y-m-d', $date) === FALSE){
                throw new Exception("Invalid date from, correct form = 'YYYY-mm-dd'");
            }
        }

        $queryStatus = modelStatus::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $queryCreative = modelCreative::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $queryPrice = modelPrice::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $queryOffer = modelOffer::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $fileName = date("m.d.Y", strtotime($date));
        $file = storage_path("matrix/daily" . "/" . $fileName . ".xlsx");

        $this->fileName = $fileName;
        $this->googleFolder = "1W3aoUw-5eUdzKTyv0izTcd7luSKm5Boa";

        $this->titleSubject = date("d F Y", strtotime($date));
        $this->titleBody = "Daily Report";

        $this->create($file, $queryStatus, $queryCreative, $queryPrice, $queryOffer, true);
    }


    protected function monthly()
    {
        $date = $this->argument('date');
        if(!$date){
            $date = date('Y-m', strtotime('-1 month'));
        } else {
            if(DateTime::createFromFormat('Y-m', $date) === FALSE){
                throw new Exception("Invalid date from, correct form = 'YYYY-mm'");
            }
        }

        $queryStatus = modelStatus::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $queryCreative = modelCreative::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $queryPrice = modelPrice::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $queryOffer = modelOffer::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $fileName = date("m.Y", strtotime($date));
        $file = storage_path("matrix/monthly" . "/" . $fileName . ".xlsx");

        $this->fileName = $fileName;
        $this->googleFolder = "14rLMCE8DjWd2nf2X7T6nPmOxEb9XjuHx";

        $this->titleSubject = date("F Y", strtotime($date));
        $this->titleBody = "Monthly Report";

        $this->create($file, $queryStatus, $queryCreative, $queryPrice, $queryOffer, true);
    }


    protected function year()
    {
        ini_set('memory_limit', '128M');

        $date = $this->argument('date');
        if(!$date){
            $date = date('Y', strtotime('-1 year'));
        } else {
            if(DateTime::createFromFormat('Y', $date) === FALSE){
                throw new Exception("Invalid date from, correct form = 'YYYY'");
            }
        }

        $queryStatus = modelStatus::where(DB::raw('DATE_FORMAT(updated_at, "%Y")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $queryCreative = modelCreative::where(DB::raw('DATE_FORMAT(updated_at, "%Y")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $queryPrice = modelPrice::where(DB::raw('DATE_FORMAT(updated_at, "%Y")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $queryOffer = modelOffer::where(DB::raw('DATE_FORMAT(updated_at, "%Y")'), $date)
            ->where('status', 3)
            ->orderBy('created_at');

        $fileName = date("Y", strtotime('-1 year'));
        $file = storage_path("matrix/year" . "/" . $fileName . ".xlsx");

        $this->fileName = $fileName;
        $this->googleFolder = "";

        $this->titleSubject = date("Y", strtotime($date));
        $this->titleBody = "Annual Report";

        $this->create($file, $queryStatus, $queryCreative, $queryPrice, $queryOffer);
    }


    protected function test()
    {
        $dateTestStatus = "2018-10-01";
        $dateTestCreative = "2018-09-24";
        $dateTestPrice = "2018-09-14";
        $dateTestOffer = "2018-09-16";
        $fileName = "2018-10-01";

        $dataStatus = modelStatus::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), $dateTestStatus)
            ->where('status', 3)
            ->orderBy('created_at');

        $dataCreative = modelCreative::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), $dateTestCreative)
            ->where('status', 3)
            ->orderBy('created_at');

        $dataPrice = modelPrice::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), $dateTestPrice)
            ->where('status', 3)
            ->orderBy('created_at');

        $dataOffer = modelOffer::where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), $dateTestOffer)
            ->where('status', 3)
            ->orderBy('created_at');

        $file = storage_path("matrix/test" . "/" . $fileName . ".xlsx");

        $this->fileName = $fileName;
        $this->googleFolder = "1W3aoUw-5eUdzKTyv0izTcd7luSKm5Boa";

        $this->titleSubject = date("d F Y", strtotime($fileName));
        $this->titleBody = "Test Report";

        $this->create($file, $dataStatus, $dataCreative, $dataPrice, $dataOffer, true);
    }


    protected function create($file, &$queryStatus, $queryCreative, $queryPrice, $queryOffer, $loadGoogle = false)
    {
        $workSheetCreative = new Worksheet();
        $workSheetPrice = new Worksheet();
        $workSheetOffer = new Worksheet();

        $spreadsheet = new Spreadsheet();
        $sheetStatus = $spreadsheet->getActiveSheet();
        $sheetStatus->setTitle("Status");

        $this->createStatus($queryStatus, $sheetStatus);

        $spreadsheet->addSheet($workSheetCreative, 1);
        $sheetCreative = $spreadsheet->getSheet(1);
        $sheetCreative->setTitle("Creative");

        $this->createCreative($queryCreative, $sheetCreative);

        $spreadsheet->addSheet($workSheetPrice, 2);
        $sheetPrice = $spreadsheet->getSheet(2);
        $sheetPrice->setTitle("Price");

        $this->createPrice($queryPrice, $sheetPrice);

        $spreadsheet->addSheet($workSheetOffer, 3);
        $sheetOffer = $spreadsheet->getSheet(3);
        $sheetOffer->setTitle("Offer");

        $this->createOffer($queryOffer, $sheetOffer);

        $writer = new Xlsx($spreadsheet);
        $writer->save($file);

        if($loadGoogle){
            $this->loadGoogleDrive($file);
        }
    }


    protected function createStatus(&$queryStatus, &$sheet)
    {
        $sheet->setCellValue("A1", "Change");
        $sheet->setCellValue("B1", "Date");
        $sheet->setCellValue("C1", "Effective Date");
        $sheet->setCellValue("D1", "User");
        $sheet->setCellValue("E1", "Sales Manager");
        $sheet->setCellValue("F1", "Account Manager");
        $sheet->setCellValue("G1", "Campaign");
        $sheet->setCellValue("H1", "LT CID");
        $sheet->setCellValue("I1", "EF CID");
        $sheet->setCellValue("J1", "Merchant");
        $sheet->setCellValue("K1", "Redirect Url");
        $sheet->setCellValue("L1", "Reason");

        $count = 2;

        $queryStatus->chunk(50, function ($dataStatus) use (&$sheet, &$count){

            foreach ($dataStatus as $iter) {

                $dataOffer = $iter->offer;
                $dataAdvertiser = $dataOffer->advertiser;

                $change = $iter->ef_status ?: $iter->lt_status;

                $sheet->setCellValue("A$count", $change);
                $sheet->setCellValue("B$count", date("m.d.Y", strtotime($iter->created_at)));
                $sheet->setCellValue("C$count", date("m.d.Y", strtotime($iter->date)));
                $sheet->setCellValue("D$count", $iter->created_param->name);
                $sheet->setCellValue("E$count", $dataOffer->manager->name);
                $sheet->setCellValue("F$count", $dataOffer->manager_account->name);
                $sheet->setCellValue("G$count", $dataOffer->campaign_name);
                $sheet->setCellValue("H$count", $dataOffer->lt_id);
                $sheet->setCellValue("I$count", $dataOffer->ef_id);
                $sheet->setCellValue("J$count", $dataAdvertiser->name);
                $sheet->setCellValue("K$count", $iter->redirect_url);
                $sheet->setCellValue("L$count", $iter->reason);

                $count++;
            }
        });
    }


    protected function createCreative(&$queryCreative, &$sheet)
    {
        $sheet->setCellValue("A1", "Change");
        $sheet->setCellValue("B1", "Date");
        $sheet->setCellValue("C1", "User");
        $sheet->setCellValue("D1", "Sales Manager");
        $sheet->setCellValue("E1", "Account Manager");
        $sheet->setCellValue("F1", "Campaign");
        $sheet->setCellValue("G1", "LT ID");
        $sheet->setCellValue("H1", "EF ID");
        $sheet->setCellValue("I1", "Merchant");
        $sheet->setCellValue("J1", "PriceIn");
        $sheet->setCellValue("K1", "PriceOut");
        $sheet->setCellValue("L1", "Link");
        $sheet->setCellValue("M1", "Cap");
        $sheet->setCellValue("N1", "Traffic");
        $sheet->setCellValue("O1", "Cap Type");
        $sheet->setCellValue("P1", "Demos");
        $sheet->setCellValue("Q1", "Restrictions");
        $sheet->setCellValue("R1", "Notes");

        $count = 2;

        $queryCreative->chunk(50, function ($dataCreative) use (&$sheet, &$count) {

            foreach ($dataCreative as $request) {

                $dataOffer = $request->offer;
                $managerSales = $dataOffer->manager->name;
                $managerAccount = $dataOffer->manager_account->name;

                $createdBy = $request->created_param->name;
                $creative = $request->creatives;

                $dataAdvertiser = $dataOffer->advertiser;

                foreach ($creative as $iter) {

                    $sheet->setCellValue("A$count", "New Creative");
                    $sheet->setCellValue("B$count", date("m.d.Y", strtotime($request->created_at)));
                    $sheet->setCellValue("C$count", $createdBy);
                    $sheet->setCellValue("D$count", $managerSales);
                    $sheet->setCellValue("E$count", $managerAccount);
                    $sheet->setCellValue("F$count", $dataOffer->campaign_name);
                    $sheet->setCellValue("G$count", $dataOffer->lt_id);
                    $sheet->setCellValue("H$count", $dataOffer->ef_id);
                    $sheet->setCellValue("I$count", $dataAdvertiser->name);
                    $sheet->setCellValue("J$count", $iter->price_in);
                    $sheet->setCellValue("K$count", $iter->price_out);
                    $sheet->setCellValue("L$count", $iter->link);
                    $sheet->setCellValue("M$count", $request->cap);
                    $sheet->setCellValue("N$count", $request->type_traffic);
                    $sheet->setCellValue("O$count", $request->cap_type->name);
                    $sheet->setCellValue("P$count", $request->demos);
                    $sheet->setCellValue("Q$count", $request->restrictions);
                    $sheet->setCellValue("R$count", $request->notes);

                    $count++;
                }
            }
        });
    }


    protected function createPrice(&$queryPrice, &$sheet)
    {
        $sheet->setCellValue("A1", "Change");
        $sheet->setCellValue("B1", "Date");
        $sheet->setCellValue("C1", "Effective Date");
        $sheet->setCellValue("D1", "User");
        $sheet->setCellValue("E1", "Sales Manager");
        $sheet->setCellValue("F1", "Account Manager");
        $sheet->setCellValue("G1", "Campaign");
        $sheet->setCellValue("H1", "LT ID");
        $sheet->setCellValue("I1", "EF ID");
        $sheet->setCellValue("J1", "Merchant");
        $sheet->setCellValue("K1", "PriceIn");
        $sheet->setCellValue("L1", "PriceOut");
        $sheet->setCellValue("M1", "Affiliate");
        $sheet->setCellValue("N1", "Reason");

        $count = 2;

        $queryPrice->chunk(50, function ($dataPrice) use (&$sheet, &$count) {

            foreach ($dataPrice as $iter) {

                $change = "Price " . $iter->getType();

                $dataOffer = $iter->offer;
                $dataAdvertiser = $dataOffer->advertiser;

                if ($iter->affiliate_all) {
                    $affiliate = "All";
                } else {
                    $dataAffiliate = $iter->affiliate;
                    $affiliate = "lt_id=" . $dataAffiliate->lt_id . ", ef_id=" . $dataAffiliate->ef_id;
                }

                $sheet->setCellValue("A$count", $change);
                $sheet->setCellValue("B$count", date("m.d.Y", strtotime($iter->created_at)));
                $sheet->setCellValue("C$count", date("m.d.Y", strtotime($iter->date)));
                $sheet->setCellValue("D$count", $iter->created_param->name);
                $sheet->setCellValue("E$count", $dataOffer->manager->name);
                $sheet->setCellValue("F$count", $dataOffer->manager_account->name);
                $sheet->setCellValue("G$count", $dataOffer->campaign_name);
                $sheet->setCellValue("H$count", $dataOffer->lt_id);
                $sheet->setCellValue("I$count", $dataOffer->ef_id);
                $sheet->setCellValue("J$count", $dataAdvertiser->name);
                $sheet->setCellValue("K$count", $iter->price_in);
                $sheet->setCellValue("L$count", $iter->price_out);
                $sheet->setCellValue("M$count", $affiliate);
                $sheet->setCellValue("N$count", $iter->reason);

                $count++;
            }
        });
    }


    protected function createOffer(&$queryOffer, &$sheet)
    {
        $sheet->setCellValue("A1", "Change");
        $sheet->setCellValue("B1", "Date");
        $sheet->setCellValue("C1", "Effective Date");
        $sheet->setCellValue("D1", "User");
        $sheet->setCellValue("E1", "Sales Manager");
        $sheet->setCellValue("F1", "Account Manager");
        $sheet->setCellValue("G1", "Campaign");
        $sheet->setCellValue("H1", "LT ID");
        $sheet->setCellValue("I1", "EF ID");
        $sheet->setCellValue("J1", "Merchant");

        $sheet->setCellValue("K1", "Category");
        $sheet->setCellValue("L1", "Traffic");
        $sheet->setCellValue("M1", "PriceIn");
        $sheet->setCellValue("N1", "PriceOut");
        $sheet->setCellValue("O1", "Pixel");
        $sheet->setCellValue("P1", "Pixel Location");
        $sheet->setCellValue("Q1", "Sample URL");

        $sheet->setCellValue("R1", "Lead Cap");
        $sheet->setCellValue("S1", "Cap Type");
        $sheet->setCellValue("T1", "Cap Redirect");
        $sheet->setCellValue("U1", "Geos");

        $sheet->setCellValue("V1", "Accepted Traffic Sources");
        $sheet->setCellValue("W1", "Affiliates Notes");
        $sheet->setCellValue("X1", "Restrictions");

        $count = 2;

        $queryOffer->chunk(50, function ($dataOffer) use (&$sheet, &$count) {

            foreach ($dataOffer as $iter) {

                $dataAdvertiser = $iter->advertiser;

                $country = $iter->getGeos();
                if ($country) {
                    $geo = implode(",", $country);
                } else {
                    $geo = "";
                }

                $sheet->setCellValue("A$count", "New Live Offer");
                $sheet->setCellValue("B$count", date("m.d.Y", strtotime($iter->created_at)));
                //$sheet->setCellValue("C$count", "Effective Date");
                $sheet->setCellValue("D$count", $iter->created_param->name);
                $sheet->setCellValue("E$count", $iter->manager->name);
                $sheet->setCellValue("F$count", $iter->manager_account->name);
                $sheet->setCellValue("G$count", $iter->campaign_name);
                $sheet->setCellValue("H$count", $iter->lt_id);
                $sheet->setCellValue("I$count", $iter->ef_id);
                $sheet->setCellValue("J$count", $dataAdvertiser->name);

                $sheet->setCellValue("K$count", $iter->offer_category->name);
                $sheet->setCellValue("L$count", $iter->accepted_traffic);
                $sheet->setCellValue("M$count", $iter->price_in);
                $sheet->setCellValue("N$count", $iter->price_out);
                $sheet->setCellValue("O$count", $iter->pixel->name);
                $sheet->setCellValue("P$count", $iter->pixel_location);
                $sheet->setCellValue("Q$count", $iter->campaign_link);

                $sheet->setCellValue("R$count", $iter->cap_lead);
                $sheet->setCellValue("S$count", $iter->cap_type->name);
                $sheet->setCellValue("T$count", $iter->redirect_url);
                $sheet->setCellValue("U$count", $geo);

                $sheet->setCellValue("V$count", $iter->accepted_traffic);
                $sheet->setCellValue("W$count", $iter->affiliate_note);
                $sheet->setCellValue("X$count", $iter->internal_note);

                $count++;
            }
        });
    }


    protected function loadGoogleDrive($file)
    {

        $googleCron = new \App\Services\GoogleCron();
        $googleDriveService = new Google_Service_Drive($googleCron->getClient());

        $fileMetadata = $googleCron->getMetadataXlsx($this->fileName, [$this->googleFolder]);

        $content = file_get_contents($file);
        $param = array(
            'data' => $content,
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'uploadType' => 'multipart',
            'fields' => 'id');

        $result = $googleDriveService->files->create($fileMetadata, $param);

        if(isset($result->id)){

            $googleLink = "https://drive.google.com/open?id=$result->id";

            $mailer = new Mailer();
            $mailer->sendDailyMatrix($googleLink, $this->titleSubject, $this->titleBody);
        }

    }
}