<?php
namespace App\Console\Commands\Cron;

use Illuminate\Console\Command;

use App\Services\Mailer as Mailer;

use App\Models\Request\Statistic as modelRequestStatistic;
use App\Models\Request\StatisticReport as modelRequestStatisticReport;
use App\Models\User as modelUser;
use App\Models\UserParamEmail as modelUserParamEmail;
use App\Models\EmailTemplate as modelEmailTemplate;

use Validator;
use DB;

use DateTime;
use DateTimeZone;
use Exception;
use PDOException;
use PHPMailer\PHPMailer\Exception AS ExceptionPHPMailer;

class StatRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/stat:request {type} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Request report';

    protected $template;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate();
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
            case "send" :
                $this->send();
                break;
            case "test" :
                $this->test();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function send()
    {
        $count = ['ok' => 0, 'error' => 0];

        $date = $this->argument('date');
        if(!$date){
            $date = date("Y-m", strtotime("-1 month"));
        } else {
            if(DateTime::createFromFormat('Y-m', $date) === FALSE){
                throw new Exception("Invalid date from, correct form = 'YYYY-mm'");
            }
        }

        var_dump(date("F Y", strtotime($date."-01")));

        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('advertiser AS a')
            ->select('a.id', 'a.lt_id', 'a.ef_id', 'a.name', DB::raw('IFNULL(rs.advertiser_contact, a.contact) as contact'), DB::raw('IFNULL(rs.advertiser_email, a.email) as email'), 'rs.from_user_id', 'rs.reason')
            ->join('advertiser_stat AS as', 'as.advertiser_id', '=', 'a.id')
            ->join('request_statistic AS rs', 'rs.advertiser_id', '=', 'a.id')
            ->where(function($query){
                $query->where('as.revenue', '>', 0)
                    ->orWhere('as.click', '>', 0);
            })
            ->where('rs.notification', 1)
            ->where(DB::raw('DATE_FORMAT(as.date, "%Y-%m")'), $date)
            ->groupBy('a.id');

        $data = $query->get();
        if($data){

            foreach($data as $iter){

                $template = $this->getTemplate();
                if(!$template){
                    throw new Exception("Email template not found");
                }

                if($iter->from_user_id){
                    $user = modelUser::find($iter->from_user_id);
                } else {
                    $user = modelUser::where('email', 'accounting@seccosquared.com')->first();
                }

                $fromName = $user->name;

                $arrTo = explode(",", $iter->email);

                $subject = $template->subject;
                $subject = str_replace("[ADVERTISER NAME]", $iter->name, $subject);

                $body = $template->body;
                $body = str_replace("[Contact Name]", $iter->contact, $body);
                $body = str_replace("[MONTH / YEAR]", date("F Y", strtotime($date."-01")), $body);
                $body = str_replace("[Sender : Secco Squared Finance Contact Name]", $fromName, $body);

                $result = $this->sendByGmail($user, $iter, $arrTo, $subject, $body);

                $dataReport = new modelRequestStatisticReport();
                $dataReport->fill([
                    'advertiser_id' => $iter->id,
                    'from_user_id' => $iter->from_user_id,
                    'subject' => $subject,
                    'body' => $body,
                    'date' => $date."-01",
                    'error' => $result['error'],
                    'status' => $result['send'] ? 3 : 4,
                ]);

                $dataReport->save();

                if($result['send']){
                    $count['ok'] ++;
                } else {
                    $count['error'] ++;
                }
            }

        } else {

        }

        var_dump("advertiser: " . count($data) . " sent: " . $count['ok'] . " error: " . $count['error']);
    }


    protected function setTemplate()
    {
        $template = modelEmailTemplate::where('status', 3)
            ->where('name', 'finance_stat_request')
            ->first();

        $this->template = $template;
    }


    protected function getTemplate()
    {
        return $this->template;
    }


    protected function sendBySmtp($dataIter, $arrTo, $subject, $body)
    {
        $dataParam = modelUserParamEmail::where('user_id', $dataIter->from_user_id)->first();
        if($dataParam){
            $dataUser = $dataParam->user;

            $mailClass = new Mailer($dataParam);

            $fromEmail = $dataParam->username;
            $fromName = $dataUser->name;

        } else {
            return ['send' => false, 'error' => "Error: User not found"];
        }

        $mail = $mailClass->getObject();
        $mail->setFrom($fromEmail, $fromName);

        if(is_array($arrTo) && count($arrTo)) {
            foreach($arrTo as $iterEmail){
                $iterEmail = trim($iterEmail);
                if($iterEmail){
                    $mail->addAddress($iterEmail);
                }
            }
        }

        $mail->addBCC('bump@go.rebump.cc');

        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;

        try {

            $mail->send();
            $result = true;
            $error = null;

        } catch (ExceptionPHPMailer $e) {

            $result = false;
            $error = $mail->ErrorInfo;

            var_dump('Mailer Error: '. $error);
        }

        return ['send' => $result, 'error' => $error];
    }


    protected function test()
    {
        $user = modelUser::find(1);

        $google = new \App\Services\Google();

        $client = $google->getClient();
        $client->setAccessToken($user->google_token);

        if($client->isAccessTokenExpired()){

            $token = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            if(isset($token['error'])){
                throw new Exception('Error: Google AccessToken ' . $token['error']);
            }
        }

        $service = new \Google_Service_Gmail($client);
        $mailer = $service->users_messages;

        $message = (new \Swift_Message('test subject'))
            ->setFrom('max@seccosquared.com')
            ->setTo('connect7@mail.ua')
            ->setContentType('text/html')
            ->setCharset('utf-8')
            ->setBody('test message');

        $msg_base64 = $this->urlsafe_b64encode($message->toString());

        $message = new \Google_Service_Gmail_Message();
        $message->setRaw($msg_base64);

        $result = $mailer->send('me', $message);

        dd($result);
    }


    protected function sendByGmail($user, $dataIter, $arrTo, $subject, $body)
    {
        if($user->google_token){

            $fromEmail = $user->email;
            $fromName = $user->name;

        } else {
            return ['send' => false, 'error' => "Error: User do not have access token"];
        }

        $google = new \App\Services\Google();

        $client = $google->getClient();
        $client->setAccessToken($user->google_token);

        if($client->isAccessTokenExpired()){

            $token = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            if($token && empty($token['error'])){
                $user->setGoogleToken($token);
                $user->save();
            } else {
                return ['send' => false, 'error' => 'Error: Google AccessToken ' . $token['error']];
            }
        }

        $validArrTo = [];
        if(is_array($arrTo) && count($arrTo)) {
            foreach($arrTo as $iterEmail){
                $iterEmail = trim($iterEmail);
                if($iterEmail){
                    $validArrTo[] = $iterEmail;
                }
            }
        }

        if(!$validArrTo){
            return ['send' => false, 'error' => 'Error: Not found recipients'];
        }

        $service = new \Google_Service_Gmail($client);
        $mailer = $service->users_messages;

        $message = (new \Swift_Message($subject))
            ->setFrom($fromEmail)
            ->setTo($validArrTo)
            ->setContentType('text/html')
            ->setCharset('utf-8')
            ->setBody($body);

        $message->addBcc('bump@go.rebump.cc');

        $msg_base64 = $this->urlsafe_b64encode($message->toString());

        $message = new \Google_Service_Gmail_Message();
        $message->setRaw($msg_base64);

        try{

            $result = $mailer->send('me', $message);

        } catch (Exception $e) {

            return ['send' => false, 'error' => $e->getMessage()];
        }

        if(isset($result->id)){
            return ['send' => true, 'error' => null];
        } else {
            return ['send' => false, 'error' => 'Google_Service_Gmail_Message.id not found'];
        }
    }


    function urlsafe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);

        return $data;
    }


}