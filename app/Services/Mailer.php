<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception AS ExceptionPHPMailer;
use Illuminate\Support\Facades\Auth;
use Exception;
use View;

use App\Models\EmailTemplate;
use App\Models\IO;
use App\Models\Offer;
use App\Models\OfferCreative;
use App\Models\Request\Cap as RequestCap;
use App\Models\Request\Status as RequestStatus;
use App\Models\Request\Price as RequestPrice;
use App\Models\Request\Creative as RequestCreative;
use App\Models\Request\MassAdjustment as MassAdjustment;
use App\Models\CreditCap as CreditCap;
use App\Models\PrePay as PrePay;

use App\Models\UserParamEmail as modelUserParamEmail;


class Mailer
{

    protected $mail;

    public function __construct(modelUserParamEmail $param = null)
    {
        $mail = new PHPMailer(true);                    // Passing `true` enables exceptions

        try {

            if($param){
                //Server settings
                $mail->SMTPDebug = 2;                                 // Enable verbose debug output
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = $param->host;                    // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = $param->username;       // SMTP username
                $mail->Password = $param->password;       // SMTP password
                $mail->SMTPSecure = $param->encryption;   // Enable TLS encryption, `ssl` also accepted
                $mail->Port = $param->port;
            } else {
                //Server settings
                $mail->SMTPDebug = 2;                                 // Enable verbose debug output
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = config('mail.host');                    // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = config('mail.username');       // SMTP username
                $mail->Password = config('mail.password');       // SMTP password
                $mail->SMTPSecure = config('mail.encryption');   // Enable TLS encryption, `ssl` also accepted
                $mail->Port = config('mail.port');
            }

        } catch (ExceptionPHPMailer $e) {
            var_dump(' Mailer Error: ', $mail->ErrorInfo);
            exit();
        }

        $this->mail = $mail;
    }

    public function getObject()
    {
        return $this->mail;
    }

    public function sendNewIO(IO $data)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'advertiser_offer_new_io_created')
            ->first();

        if($template) {

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);
            $subject = str_replace("[I/O NAME]", $data->campaign_name, $subject);

            $ioViewUrl = config('app.url') . "/admin/io/view/$data->id";
            $body = str_replace("[VIEW_URL]", $ioViewUrl, $body);
            $body = str_replace("[GOOGLE_URL]", $data->google_url, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

               $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendDeclineIO(IO $data)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'advertiser_offer_io_declined')
            ->first();

        if($template) {

            $template->to = str_replace("[Author]", $data->created_param->email, $template->to);

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $advertiser = $data->advertiser;
            $subject = str_replace("[Advertiser Name]", $advertiser->name, $subject);

            $body = str_replace("[GOOGLE_URL]", $data->google_url, $body);
            $body = str_replace("[Advertiser Name]", $advertiser->name, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendIOPendingSignature(IO $data)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'advertiser_offer_io_pending_signature')
            ->first();

        if($template) {

            $template->to = str_replace("[Author]", $data->created_param->email, $template->to);

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $body = str_replace("[Docusign_URL]", config('services.docusign.document_detail') . $data->docusign_id, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            $file_pdf = public_path($data->path_docusign . $data->docusign_file . ".pdf");

            $mail->AddAttachment($file_pdf, $data->docusign_file,  $encoding = 'base64', $type = 'application/pdf');

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendNewOffer(Offer $data, $creative)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'advertiser_offer_new_offer_created')
            ->first();

        if($template) {

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);
            $body = str_replace("[Campaign_name]", $data->campaign_name, $body);

            $offerViewUrl = config('app.url') . "/admin/offer/view/$data->id";
            $body = str_replace("[Offer_URL]", $offerViewUrl, $body);

            $offerDetails = View::make('template.email.offer.update', ['data' => $data, 'dataCreative' => $creative])->render();
            $body = str_replace("[Details]", $offerDetails, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendDeclineOffer(Offer $data, $reason)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'advertiser_offer_new_offer_declined')
            ->first();

        if($template) {

            $template->to = str_replace("[Author]", $data->created_param->email, $template->to);

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);
            $body = str_replace("[Campaign_name]", $data->campaign_name, $body);
            $body = str_replace("[Reason]", $reason, $body);

            $offerEditUrl = config('app.url') . "/admin/offer/edit-new/$data->id";
            $body = str_replace("[Offer_Edtit_Url]", $offerEditUrl, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendUpdateDeclineOffer(Offer $data, Offer $dataOld, $creative)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'advertiser_offer_declined_new_offer_updated')
            ->first();

        if($template) {

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Campaign_name]", $data->campaign_name, $subject);

            $offerViewUrl = config('app.url') . "/admin/offer/view/$data->id";
            $body = str_replace("[Offer_URL]", $offerViewUrl, $body);

            $offerDetails = View::make('template.email.offer.update', ['data' => $dataOld, 'dataCreative' => $creative])->render();
            $body = str_replace("[Details]", $offerDetails, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendNewRequestCap(RequestCap $data)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_cap')
            ->first();

        if($template) {

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);

            $capRequestViewUrl = config('app.url') . "/admin/request/cap/view/$data->id";
            $body = str_replace("[Cap_Request_Url]", $capRequestViewUrl, $body);

            $capRequestDetails = View::make('template.email.request.cap.new', ['data' => $data])->render();
            $body = str_replace("[Details]", $capRequestDetails, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendDeclineCapRequest(RequestCap $data, $reason)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_cap_decline')
            ->first();

        if($template) {

            $template->to = str_replace("[Author]", $data->created_param->email, $template->to);

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);

            $capRequestEditUrl = config('app.url') . "/admin/request/cap/edit/$data->id";
            $body = str_replace("[Cap_Request_Edit_Url]", $capRequestEditUrl, $body);
            $body = str_replace("[Campaign_name]", $data->offer->campaign_name, $body);
            $body = str_replace("[Reason]", $reason, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendNewRequestStatus(RequestStatus $data)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_offer_status')
            ->first();

        if($template) {

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);

            $statusRequestViewUrl = config('app.url') . "/admin/request/status/view/$data->id";
            $body = str_replace("[Status_Request_Url]", $statusRequestViewUrl, $body);

            $statusRequestDetails = View::make('template.email.request.status.new', ['data' => $data])->render();
            $body = str_replace("[Details]", $statusRequestDetails, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendDeclineRequestStatus(RequestStatus $data, $reason)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_offer_status_declined')
            ->first();

        if($template) {

            $template->to = str_replace("[Author]", $data->created_param->email, $template->to);

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);

            $statusRequestEditUrl = config('app.url') . "/admin/request/status/edit/$data->id";
            $body = str_replace("[Status_Request_Edit_Url]", $statusRequestEditUrl, $body);
            $body = str_replace("[Campaign_name]", $data->offer->campaign_name, $body);
            $body = str_replace("[Reason]", $reason, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendNewRequestPrice(RequestPrice $data)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_price_change')
            ->first();

        if($template) {

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);

            $priceRequestViewUrl = config('app.url') . "/admin/request/price/view/$data->id";
            $body = str_replace("[Price_Request_Url]", $priceRequestViewUrl, $body);

            $priceRequestDetails = View::make('template.email.request.price.new', ['data' => $data])->render();
            $body = str_replace("[Details]", $priceRequestDetails, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendDeclinePriceRequest(RequestPrice $data, $reason)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_price_change_declined')
            ->first();

        if($template) {

            $template->to = str_replace("[Author]", $data->created_param->email, $template->to);

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);

            $priceRequestEditUrl = config('app.url') . "/admin/request/price/edit/$data->id";
            $body = str_replace("[Price_Request_Edit_Url]", $priceRequestEditUrl, $body);
            $body = str_replace("[Campaign_name]", $data->offer->campaign_name, $body);
            $body = str_replace("[Reason]", $reason, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendNewRequestCreative(RequestCreative $data, $creative)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_creative')
            ->first();

        if($template) {

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);

            $creativeRequestViewUrl = config('app.url') . "/admin/request/creative/view/$data->id";
            $body = str_replace("[Creative_Request_View_Url]", $creativeRequestViewUrl, $body);

            $priceRequestDetails = View::make('template.email.request.creative.new', ['data' => $data, 'creative' => $creative])->render();
            $body = str_replace("[Details]", $priceRequestDetails, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendDeclineRequestCreative(RequestCreative $data, $reason)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_creative_declined')
            ->first();

        if($template) {

            $template->to = str_replace("[Author]", $data->created_param->email, $template->to);

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);
            $body = str_replace("[Campaign_name]", $data->offer->campaign_name, $body);
            $body = str_replace("[Reason]", $reason, $body);

            $creativeEditUrl = config('app.url') . "/admin/request/creative/edit/$data->id";
            $body = str_replace("[Creative_Request_Edit_Url]", $creativeEditUrl, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendUpdateDeclineRequestCreative(RequestCreative $data, RequestCreative $dataOld, $creativeOld)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_declined_creative_updated')
            ->first();

        if($template) {

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Campaign_name]", $data->offer->campaign_name, $subject);

            $creativeViewUrl = config('app.url') . "/admin/request/creative/view/$data->id";
            $body = str_replace("[Offer_URL]", $creativeViewUrl, $body);

            $creativeDetails = View::make('template.email.request.creative.update', ['data' => $dataOld, 'dataCreative' => $creativeOld])->render();
            $body = str_replace("[Details]", $creativeDetails, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendNewRequestMassAdjustment(MassAdjustment $data)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_mass_adjustment')
            ->first();

        if($template) {

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);

            $massAdjustmentRequestViewUrl = config('app.url') . "/admin/request/massadjustment/view/$data->id";
            $body = str_replace("[Mass_Adjustment_Url]", $massAdjustmentRequestViewUrl, $body);

            $massAdjustmentRequestDetails = View::make('template.email.request.massadjustment.new', ['data' => $data])->render();
            $body = str_replace("[Details]", $massAdjustmentRequestDetails, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendDeclineRequestMassAdjustment(MassAdjustment $data, $reason)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'request_request_mass_adjustment_declined')
            ->first();

        if($template) {

            $template->to = str_replace("[Author]", $data->created_param->email, $template->to);

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Author]", $data->created_param->name, $subject);

            $body = str_replace("[Reason]", $reason, $body);

            $massAdjustmentEditUrl = config('app.url') . "/admin/request/massadjustment/edit/$data->id";
            $body = str_replace("[Mass_Adjustment_Url_Edit]", $massAdjustmentEditUrl, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }

    public function sendDailyMatrix($googleLink, $titleSubject, $titleBody)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'chron_daily_matrix')
            ->first();

        if($template) {

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Date]", $titleSubject, $subject);

            $body = str_replace("[Type Report]", $titleBody, $body);
            $body = str_replace("[Google_Drive_Url]", $googleLink, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendPrePayLimit(PrePay $data)
    {
        $template = EmailTemplate::where('status', 3)
            ->where('name', 'finance_pre_pay_approaching_limit')
            ->first();

        if($template) {

            $advertiser = $data->advertiser;
            $managerSales = $advertiser->manager;
            $managerAccount = $advertiser->manager_account;

            if($managerSales){
                $template->to = str_replace("[Sales Manager]", $managerSales->email, $template->to);
            } else {
                $template->to = str_replace("[Sales Manager]", "", $template->to);
            }
            if($managerAccount){
                $template->to = str_replace("[Account Manager]", $managerAccount->email, $template->to);
            } else {
                $template->to = str_replace("[Account Manager]", "", $template->to);
            }

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Advertiser Name]", $advertiser->name, $subject);
            $body = str_replace("[Advertiser Name]", $advertiser->name, $body);


            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }


    public function sendCreditCapLimit(CreditCap $data)
    {

        $template = EmailTemplate::where('status', 3)
            ->where('name', 'finance_credit_cap_approaching_limit')
            ->first();

        if($template) {

            $advertiser = $data->advertiser;
            $managerSales = $advertiser->manager;
            $managerAccount = $advertiser->manager_account;

            if($managerSales){
                $template->to = str_replace("[Sales Manager]", $managerSales->email, $template->to);
            } else {
                $template->to = str_replace("[Sales Manager]", "", $template->to);
            }
            if($managerAccount){
                $template->to = str_replace("[Account Manager]", $managerAccount->email, $template->to);
            } else {
                $template->to = str_replace("[Account Manager]", "", $template->to);
            }

            $arrTo = explode(",", $template->to);

            $subject = $template->subject;
            $body = $template->body;

            $subject = str_replace("[Advertiser Name]", $advertiser->name, $subject);
            $body = str_replace("[Advertiser Name]", $advertiser->name, $body);

            $mail = $this->mail;
            $mail->setFrom($template->from_email, $template->from_name);

            if(is_array($arrTo) && count($arrTo)) {
                foreach($arrTo as $iter){
                    $iter = trim($iter);
                    if($iter){
                        $mail->addAddress($iter);
                    }
                }
            }

            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;

            try {

                $result = $mail->send();

            } catch (ExceptionPHPMailer $e) {
                var_dump('Mailer Error: '. $mail->ErrorInfo);
                return false;
            }

            return $result;

        } else {
            var_dump('Mailer Error: template not found');
            return false;
        }
    }
}