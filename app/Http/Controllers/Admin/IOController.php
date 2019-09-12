<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use DB;
use App\Models\IO;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use App\Models\IOTemplateDoc;
use App\Models\TermTemplate;
use App\Models\Network;
use App\Models\Advertiser;
use App\Models\Frequency;
use App\Models\Currency;
use App\Models\PipeDrive\Deal;
use App\Models\TrafficPpc;

use App\Services\Validator\EmailString;
use App\Services\Validator\OneRequiredArray;
use App\Services\PhpWord;
use App\Services\Mailer;
use App\Services\Docusign\Core as Docusign;

use DateTime;
use PHPMailer\PHPMailer\Exception as ExceptionPHPMailer;
use Exception;
use PDOException;
use Validator;

class IOController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:request_new_io'], ['only' => ['add', 'save']]);
        $this->middleware(['permission:todo_new_io'], ['only' => ['index', 'ajaxGet', 'approve', 'decline', 'saveApprove', 'upload']]);
    }


    public function index()
    {
        $model = new IO();
        $modelUser = new User();

        $dataManager = $modelUser->getManager();

        return view('admin.io.index', [
            'auth' => Auth::user(),
            'dataManager' => $dataManager,
            'dataStatus' => $model->arrStatus,
        ]);
    }


    public function ajaxGet(Request $request)
    {

        $columns     = ['i.id', 'a.name', 'i.campaign_name', 'i.status', 'u.name', 'i.created_at'];
        $columnsLike = ['i.id', 'a.name', 'i.campaign_name', 'i.status', 'u.name', DB::raw('DATE_FORMAT(i.created_at, "%b %e, %Y %T")')];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('io AS i')
            ->select('i.id', 'a.name AS advertiser_name', 'i.campaign_name', 'i.status', 'u.name AS created_name', DB::raw('DATE_FORMAT(i.created_at, "%b %e, %Y %T") AS created_at'), 'i.google_url', 'i.docusign_google_url')
            ->join('advertiser AS a', 'a.id', '=', 'i.advertiser_id')
            ->join('users AS u', 'u.id', '=', 'i.created_by_id')
            ->orderBy($columns[$order], $dir);

        if($searchValue) {

            $query->where(function ($queryLike) use ($request, $columnsLike, $searchValue) {

                foreach ($columnsLike as $key => $name) {
                    if ($request->columns[$key]['searchable']) {
                        $queryLike->orWhere($name, 'like', "%$searchValue%");
                    }
                }
            });
        }

        if($request->created_by){
            $query->where('i.created_by_id', $request->created_by);
        }
        if($request->status){
            $query->where('i.status', $request->status);
        }

        $totalFilter = $query->count();
        $query->offset($start)->limit($length);

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('io')->count(),
            "recordsFiltered" => $totalFilter,
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function view($id)
    {
        $data = IO::findOrFail($id);

        return view('admin.io.view', [
            'data' => $data,
        ]);
    }


    public function add($deal_id = null)
    {

        $modelIO = new IO();
        $modelUser = new User();
        $modelDoc = new IOTemplateDoc();
        $modelTerm = new TermTemplate();
        $modelNetwork = new Network();
        $modelFrequency = new Frequency();
        $modelCurrency = new Currency();
        $modelDeal = new Deal();
        $modelTraffic = new TrafficPpc();

        $dataNetwork = $modelNetwork->getNetwork();
        $dataTerm = $modelTerm->getTemplate();
        $dataDoc = $modelDoc->getTemplate();
        $dataManager = $modelUser->getManager();
        $dataManagerAccount = $modelUser->getManagerAccount();
        $dataFrequency = $modelFrequency->getFrequency();
        $dataCurrency = $modelCurrency->getCurrency();
        $dataTrafficPpc = $modelTraffic->getData();
        $dataCountry = Country::all();
        $dataState = State::all();

        $advertiserName = null;

        if($deal_id){
            $dataDeal = $modelDeal::find($deal_id);
            if($dataDeal){
                $dataAdvertiser = Advertiser::where('pipedrive_id', $dataDeal->pd_organization_id)->first();
                if(!$dataAdvertiser){
                    $dataAdvertiser = Advertiser::where('name', $dataDeal->advertiser_name)->first();
                }
                if($dataAdvertiser){
                    $modelIO->advertiser_id = $dataAdvertiser->id;
                    $advertiserName = $dataAdvertiser->name;
                }
                $modelIO->campaign_name = $dataDeal->io_campaign_name;
                $modelIO->pipedrive_id = $dataDeal->pd_deal_id;
            }
        }

        return view('admin.io.add', [
            'auth' => Auth::user(),
            'data' => $modelIO,
            'dataDoc' => $dataDoc,
            'dataManager' => $dataManager,
            'dataManagerAccount' => $dataManagerAccount,
            'dataCountry' => $dataCountry,
            'dataState' => $dataState,
            'dataTerm' => $dataTerm,
            'dataNetwork' => $dataNetwork,
            'dataFrequency' => $dataFrequency,
            'dataCurrency' => $dataCurrency,
            'dataTrafficPpc' => $dataTrafficPpc,
            'advertiser_label' => $advertiserName,
        ]);
    }


    public function saveAdd(Request $request)
    {

        $this->validate($request, [
            'advertiser' => 'required|integer',
            'company_contact' => 'required|max:255',
            'company_phone' => 'required|max:63',
            'company_fax' => 'nullable|max:63',
            'company_email' => ['required', 'string', 'max:255', new EmailString()],
            'company_street1' => 'required|max:255',
            'company_street2' => 'nullable|max:255',
            'company_city' => 'required|max:255',
            'company_state' => 'nullable|max:7',
            'company_country' => 'required|max:7',
            'company_zip' => 'required|max:63',
            'campaign_name' => 'required|max:255',
            'prepay' => 'required|integer',
            'prepay_amount' => 'required_if:prepay,1|nullable|integer',
            'template_document' => 'nullable|integer',
            'template_document_custom' => 'nullable|max:255|string',
            'frequency_id' => 'nullable|integer',
            'frequency_custom' => 'nullable|max:255|string',
            'governing' => 'integer',
            'gov_type' => 'required',
            'gov_date' => 'required_if:gov_type,date|date|nullable',

            'restricted_option' => 'required',
            'term' => 'required|integer',
            'mobile_attribut_platform' => 'required_if:term,7',
            'note' => 'nullable|string',

            'secco_contact' => 'required|max:255',
            'secco_phone' => 'required|max:63',
            'secco_fax' => 'required|max:63',
            'secco_email' => 'required|max:255',
            'manager_account_id' => 'nullable|integer',
            'billing_contact' => 'required|max:255',
            'billing_street1' => 'required|max:255',
            'billing_street2' => 'nullable|max:255',
            'billing_city' => 'required|max:255',
            'billing_state' => 'nullable|max:7',
            'billing_country' => 'required|max:7',
            'billing_zip' => 'required|max:63',
            'currency' => 'required|integer',
            'cp_param' => [new OneRequiredArray()],
            'traffic_sources' => 'nullable|array',
            'traffic_ppc' => 'nullable|array',
            'pipedrive_id' => 'nullable|integer',
        ]);

        $auth = Auth::user();
        $dataAdvertiser = Advertiser::findOrFail($request->advertiser);

        $data = new IO();
        $data->fill([
            'campaign_name' => $request->campaign_name,
            'advertiser_id' => $request->advertiser,
            'currency_id' => $request->currency,
            'compCpc' => $request->cp_param['cpc'] ? : null,
            'compCpa' => $request->cp_param['cpa'] ? : null,
            'compCpl' => $request->cp_param['cpl'] ? : null,
            'compCpm' => $request->cp_param['cpm'] ? : null,
            'compCpd' => $request->cp_param['cpd'] ? : null,
            'compCpi' => $request->cp_param['cpi'] ? : null,
            'compCps' => $request->cp_param['cps'] ? : null,
            'traffic_incent_name',
            'secco_contact' => $request->secco_contact,
            'secco_email' => $request->secco_email,
            'secco_phone' => $request->secco_phone,
            'secco_fax' => $request->secco_fax,
            'company_name' => $dataAdvertiser->name,
            'company_contact' => $request->company_contact,
            'company_email' => $request->company_email,
            'company_country' => $request->company_country,
            'company_state' => $request->company_state,
            'company_city' => $request->billing_city,
            'company_street1' => $request->company_street1,
            'company_street2' => $request->company_street2,
            'company_zip' => $request->company_zip,
            'company_phone' => $request->company_phone,
            'company_fax' => $request->company_fax,
            'manager_account_id' => $request->manager_account_id ? : 0,
            'billing_contact' => $request->billing_contact,
            'billing_street1' => $request->billing_street1,
            'billing_street2' => $request->billing_street2,
            'billing_country' => $request->billing_country,
            'billing_state' => $request->billing_state,
            'billing_city' => $request->billing_city,
            'billing_zip' => $request->billing_zip,
            'prepay' => $request->prepay ? 1 : 0,
            'prepay_amount' => $request->prepay_amount ? : 0,
            'gov_type' => $request->gov_type,
            'gov_date' => $request->gov_date ? : null,
            'governing' => $request->governing ? 1 : 0,
            'status' => 1,
            'term_id' => $request->term ? : 0,
            'mobile_attribut_platform' => $request->mobile_attribut_platform ? : null,
            'template_document_id' => $request->template_document ? : 0,
            'template_document_custom' => $request->template_document_custom ? : null,
            'note' => $request->note,
//            'time',
            'frequency_id' => $request->frequency_id ? : 0,
            'frequency_custom' => $request->frequency_custom ? : null,
            'created_by' => $auth->email,
            'created_by_id'=> $auth->id,
            'pipedrive_id' => $request->pipedrive_id ? : 0,
            'order_number' => IO::max('order_number') + 1, /* IO::count() + config('constant.io_start_index') + (int) $request->advertiser */
        ]);

        $trafficSources = $request->traffic_sources ? : [];
        if(in_array("all", $trafficSources)){

            $data->fill([
                'traffic_search' => 1,
                'traffic_banner' => 1,
                'traffic_popup' => 1,
                'traffic_context' => 1,
                'traffic_exit' => 1,
                'traffic_incent' => 1,
                'traffic_path' => 1,
                'traffic_social' => 1,
                'traffic_email' => 1,
                'traffic_mobile' => 1,
            ]);

        } else {

            $data->traffic_search = in_array("search", $trafficSources) ? 1 : 0;
            $data->traffic_banner = in_array("banner", $trafficSources) ? 1 : 0;
            $data->traffic_popup = in_array("popup", $trafficSources) ? 1 : 0;
            $data->traffic_context = in_array("context", $trafficSources) ? 1 : 0;
            $data->traffic_exit = in_array("exit", $trafficSources) ? 1 : 0;
            $data->traffic_incent = in_array("incent", $trafficSources) ? 1 : 0;
            $data->traffic_path = in_array("path", $trafficSources) ? 1 : 0;
            $data->traffic_social = in_array("social", $trafficSources) ? 1 : 0;
            $data->traffic_email = in_array("email", $trafficSources) ? 1 : 0;
            $data->traffic_mobile = in_array("mobile", $trafficSources) ? 1 : 0;

        }

        $restrictedOption = $request->restricted_option;
        $data->fill([
            'restricted_no_adult' => in_array("no_adult", $restrictedOption) ? 1 : 0,
            'restricted_no_incent' => in_array("no_incent", $restrictedOption) ? 1 : 0,
            'restricted_no_rebrokering' => in_array("no_rebrokering", $restrictedOption) ? 1 : 0,
            'restricted_no_affiliate_net' => in_array("no_affiliate_network", $restrictedOption) ? 1 : 0,
            'restricted_none' => in_array("none", $restrictedOption) ? 1 : 0,
        ]);

        if ($request->gov_date && $request->gov_type == 'date'){
            $govDate = new DateTime($request->gov_date);
            $data->governing_term = "This insertion order shall be governed by the Terms and Conditions for Advertising, which were agreed to by the parties on {$govDate->format("F j, Y")}, the terms of which are incorporated herein by reference.";
        }

        $clear_AdvertiserName = str_replace('/', ' ',filter_var($dataAdvertiser->name, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $clear_CampaignName = filter_var($data->campaign_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $clear_CampaignName = str_replace('/', ' ', $clear_CampaignName);

        $data->google_file_name = trim($clear_AdvertiserName) . '-' . trim($clear_CampaignName) . '-' . $data->order_number;
        $data->save();

        if ($request->traffic_ppc && is_array($request->traffic_ppc)) {
            $data->traffic_ppc()->detach();
            foreach($request->traffic_ppc as $iter) {
                $data->traffic_ppc()->attach((int) $iter);
            }
        }

        $phpWord = new PhpWord();
        $phpWord->createDocx($data);

        $result = $data->createGoogleDriveDocx();
        if($result){

            DB::beginTransaction();

            try {

                $data->save();

                if ($data->pipedrive_id) {
                    Deal::where('pd_deal_id', $data->pipedrive_id)->update(['status' => 3]);
                }
                DB::commit();

            } catch (Exception $e){

                DB::rollBack();

                return redirect()->back()->withInput($request->all())->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'autohide' => 0,
                ]]);


            } catch (PDOException $e){

                DB::rollBack();

                return redirect()->back()->withInput($request->all())->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'autohide' => 0,
                ]]);
            }

            $mailer = new Mailer();
            $mailer->sendNewIO($data);

            return redirect()->route('admin.io.add')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Insertion order <a target='_blank' href='".$data->google_url."'>$data->google_file_name</a> has been created !",
                'autohide' => 0,
            ]]);

        } else {

            return redirect()->back()->withInput($request->all())->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Google Drive return error, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function approve($id)
    {
        $data = IO::where('id', $id)->where('status', 1)->first();

        if($data){

            $modelUser = new User();
            $dataManager = $modelUser->getDocusignManager();

            return view('admin.io.approve', [
                'dataManager' => $dataManager,
                'dataAdvertiser' => $data->advertiser,
                'data' => $data,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "IO not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function saveApprove(Request $request)
    {
        $this->validate($request, [
            'credit' => 'nullable|integer',
            'docusign_manager_id' => 'required|integer',
            'docusign_name_advertiser' => 'required|max:63',
            'docusign_email_advertiser' => 'required|email|max:63',
        ]);

        $data = IO::where('id', $request->id)->where('status', 1)->first();
        if($data){

            $data->fill([
                'credit' => $request->credit ? 1 : 0,
                'docusign_manager_id' => $request->docusign_manager_id,
                'docusign_name_advertiser' => $request->docusign_name_advertiser,
                'docusign_email_advertiser' => $request->docusign_email_advertiser,
            ]);

            $word = new PhpWord();
            $docusign = new Docusign();

            try{

                $word->converPDF($data);
                $envelopeId = $docusign->loadDocument($data, "created");

                $data->fill([
                    'status' => 6,
                    'file_pdf_exist' => 1,
                    'docusign_id' => $envelopeId,
                ]);
                $data->save();


            } catch (PDOException $e){

                return redirect()->back()->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'autohide' => 0,
                ]]);

            } catch (Exception $e){

                return redirect()->back()->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'autohide' => 0,
                ]]);
            }

            //$mailer = new Mailer();
            //$mailer->sendIOPendingSignature($data);

            return redirect()->route('admin.io.index')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Insertion order $data->campaign_name has been upload to Docusign.",
                'autohide' => 0,
            ]]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "IO not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function listenApiDocusign(Request $request)
    {
        $content = $request->getContent();
        if($content) {

            $xml = simplexml_load_string($content, "SimpleXMLElement", LIBXML_PARSEHUGE);

            $envelope_id = (string)$xml->EnvelopeStatus->EnvelopeID;
            $time_generated = (string)$xml->EnvelopeStatus->TimeGenerated;
            $status = (string)$xml->EnvelopeStatus->Status;

            if ($status == "Completed" && $envelope_id) {

                $data = IO::where('docusign_id', $envelope_id)->where('status', 6)->first();
                if ($data) {

                    $docusign =  new \App\Services\Docusign\Core();

                    $file = $docusign->downloadDocument($data);
                    $data->docusign_file = $file;
                    $data->status = 3;
                    $data->save();
                }
            }
        }
    }


    public function checkApiDocusign(Request $request)
    {
        $data = IO::where('id', $request->id)->where('status', 6)->whereNotNull('docusign_id')->first();
        if($data){

            $docusign =  new \App\Services\Docusign\Core();

            try{

                $status = $docusign->getDocumentInfo($data);
                if($status == "completed"){

                    $file = $docusign->downloadDocument($data);
                    $data->docusign_file = $file;
                    $data->createGoogleDrivePdf();
                    $data->status = 3;
                    $data->save();

                    $alert = [
                        'type' => 'success',
                        'title' => 'Success!',
                        'message' => "Document $data->campaign_name is signed",
                        'hide' => 1,
                    ];

                    return response()->json(['status' => 'ok', 'alert' => $alert]);

                } else {

                    $alert = [
                        'type' => 'success',
                        'title' => 'Success!',
                        'message' => "Document $data->campaign_name in the signing process, current status = $status",
                        'hide' => 1,
                    ];

                    return response()->json(['status' => 'processing', 'alert' => $alert]);
                }

            } catch (Exception $e) {

                $alert = [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'hide' => 0,
                ];

                return response()->json(['status' => 'error', 'alert' => $alert]);
            }

        } else {

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => 'IO not found',
                'hide' => 0,
            ];

            return response()->json(['status' => 'error', 'alert' => $alert]);
        }

    }


    public function decline($id)
    {
        $data = IO::where('id', $id)->whereIn('status', [1])->first();
        if($data){

            $mailer = new Mailer();

            $data->status = 2;
            $data->save();

            $mailer->sendDeclineIO($data);

            return redirect()->route('admin.io.index')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "IO $data->campaign_name has been declined.",
                'autohide' => 0,
            ]]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "IO not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function saveUpload(Request $request)
    {
        $rule = [
            'file' => 'required|file|mimes:pdf',
            'id' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rule);

        if($validator->fails()){
            return response()->json($validator->errors()->first(), 400);
        }

        $data = IO::where('id', $request->input('id'))->whereIn('status', [1, 6])->first();
        if($data == false){
            return response()->json(['Error', 'IO not found'], 400);
        }

        $file = $request->file('file');
        $filePath = $file->getRealPath();
        $path = public_path($data->path_docusign . $data->google_file_name . ".pdf");

        $result = copy($filePath, $path);
        @unlink($filePath);

        if($result){

            $data->docusign_file = $data->google_file_name;
            $data->createGoogleDrivePdf();
            $data->status = 3;
            $data->save();

            return response()->json(['success', 'path' => $filePath, 'path_new' => $path, 'id' => $request->input('id')], 200);
        } else {
            return response()->json(['Error', 'File has not been saved'], 400);
        }
    }


    public function deleteUpload(Request $request)
    {
        $rule = [
            'id' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rule);

        if($validator->fails()){

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $validator->errors()->first(),
                'hide' => 0,
            ];

            return response()->json(['status' => 'error', 'alert' => $alert], 400);
        }

        $data = IO::where('id', $request->input('id'))->first();
        if($data == false){

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => 'IO not found',
                'hide' => 0,
            ];

            return response()->json(['status' => 'error', 'alert' => $alert], 400);
        }

        $data->docusign_file = null;
        $data->status = 1;
        $google_result = $data->deleteGoogleDrivePdf();

        if($google_result['status'] == 'error'){

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $google_result['message'],
                'hide' => 0,
            ];

            return response()->json(['status' => 'success', 'alert' => $alert], 400);
        }

        $alert = [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "File for $data->campaign_name has been removed",
            'hide' => 1,
        ];

        return response()->json(['status' => 'success', 'alert' => $alert], 200);
    }


    public function individual($deal_id = null)
    {
        $modelIO = new IO();
        $modelUser = new User();
        $modelNetwork = new Network();
        $modelCurrency = new Currency();
        $modelDeal = new Deal();

        $dataNetwork = $modelNetwork->getNetwork();
        $dataManager = $modelUser->getManager();
        $dataManagerAccount = $modelUser->getManagerAccount();
        $dataCurrency = $modelCurrency->getCurrency();
        $dataCountry = Country::all();
        $dataState = State::all();

        $advertiserName = null;

        if($deal_id){
            $dataDeal = $modelDeal::find($deal_id);
            if($dataDeal){
                $dataAdvertiser = Advertiser::where('pipedrive_id', $dataDeal->pd_organization_id)->first();
                if(!$dataAdvertiser){
                    $dataAdvertiser = Advertiser::where('name', $dataDeal->advertiser_name)->first();
                }
                if($dataAdvertiser){
                    $modelIO->advertiser_id = $dataAdvertiser->id;
                    $advertiserName = $dataAdvertiser->name;
                }
                $modelIO->campaign_name = $dataDeal->io_campaign_name;
                $modelIO->pipedrive_id = $dataDeal->pd_deal_id;
            }
        }

        return view('admin.io.individual', [
            'auth' => Auth::user(),
            'data' => $modelIO,
            'dataNetwork' => $dataNetwork,
            'dataManager' => $dataManager,
            'dataManagerAccount' => $dataManagerAccount,
            'dataCountry' => $dataCountry,
            'dataState' => $dataState,
            'dataCurrency' => $dataCurrency,
            'advertiser_label' => $advertiserName,
        ]);
    }


    public function saveIndividual(Request $request)
    {

        $this->validate($request, [
            'advertiser' => 'required|integer',
            'company_contact' => 'required|max:255',
            'company_phone' => 'required|max:63',
            'company_fax' => 'nullable|max:63',
            'company_email' => ['required', 'string', 'max:255', new EmailString()],
            'company_street1' => 'required|max:255',
            'company_street2' => 'nullable|max:255',
            'company_city' => 'required|max:255',
            'company_state' => 'nullable|max:7',
            'company_country' => 'required|max:7',
            'company_zip' => 'required|max:63',
            'campaign_name' => 'required|max:255',
            'prepay' => 'required|integer',
            'prepay_amount' => 'required_if:prepay,1|nullable|integer',
            'governing' => 'integer',
            'gov_type' => 'required',
            'gov_date' => 'required_if:gov_type,date|date|nullable',
            'secco_contact' => 'required|max:255',
            'secco_phone' => 'required|max:63',
            'secco_fax' => 'required|max:63',
            'secco_email' => 'required|max:255',
            'manager_account_id' => 'nullable|integer',
            'billing_contact' => 'required|max:255',
            'billing_street1' => 'required|max:255',
            'billing_street2' => 'nullable|max:255',
            'billing_city' => 'required|max:255',
            'billing_state' => 'nullable|max:7',
            'billing_country' => 'required|max:7',
            'billing_zip' => 'required|max:63',
            'currency' => 'required|integer',
            'pipedrive_id' => 'nullable|integer',
            'file_io' => 'required|file|mimes:docx'
        ]);

        $auth = Auth::user();
        $dataAdvertiser = Advertiser::findOrFail($request->advertiser);

        $data = new IO();
        $data->fill([
            'campaign_name' => $request->campaign_name,
            'advertiser_id' => $request->advertiser,
            'currency_id' => $request->currency,
            'compCpc' => null,
            'compCpa' => null,
            'compCpl' => null,
            'compCpm' => null,
            'compCpd' => null,
            'compCpi' => null,
            'compCps' => null,
            'traffic_incent_name',
            'secco_contact' => $request->secco_contact,
            'secco_email' => $request->secco_email,
            'secco_phone' => $request->secco_phone,
            'secco_fax' => $request->secco_fax,
            'company_name' => $dataAdvertiser->name,
            'company_contact' => $request->company_contact,
            'company_email' => $request->company_email,
            'company_country' => $request->company_country,
            'company_state' => $request->company_state,
            'company_city' => $request->billing_city,
            'company_street1' => $request->company_street1,
            'company_street2' => $request->company_street2,
            'company_zip' => $request->company_zip,
            'company_phone' => $request->company_phone,
            'company_fax' => $request->company_fax,
            'manager_account_id' => $request->manager_account_id ? : 0,
            'billing_contact' => $request->billing_contact,
            'billing_street1' => $request->billing_street1,
            'billing_street2' => $request->billing_street2,
            'billing_country' => $request->billing_country,
            'billing_state' => $request->billing_state,
            'billing_city' => $request->billing_city,
            'billing_zip' => $request->billing_zip,
            'prepay' => $request->prepay ? 1 : 0,
            'prepay_amount' => $request->prepay_amount ? : 0,
            'gov_type' => $request->gov_type,
            'gov_date' => $request->gov_date ? : null,
            'governing' => $request->governing ? 1 : 0,
            'status' => 1,
            'created_by' => $auth->email,
            'created_by_id'=> $auth->id,
            'pipedrive_id' => $request->pipedrive_id ? : 0,
            'order_number' => IO::max('order_number') + 1, /* IO::count() + config('constant.io_start_index') + (int) $request->advertiser */
        ]);

        $data->traffic_search = 0;
        $data->traffic_banner = 0;
        $data->traffic_popup = 0;
        $data->traffic_context = 0;
        $data->traffic_exit = 0;
        $data->traffic_incent = 0;
        $data->traffic_path = 0;
        $data->traffic_social = 0;
        $data->traffic_email = 0;
        $data->traffic_mobile = 0;

        $data->fill([
            'restricted_no_adult' => 0,
            'restricted_no_incent' => 0,
            'restricted_no_rebrokering' => 0,
            'restricted_no_affiliate_net' => 0,
            'restricted_none' => 0,
        ]);

        $data->governing_term = null;

        $clear_AdvertiserName = str_replace('/', ' ',filter_var($dataAdvertiser->name, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $clear_CampaignName = filter_var($data->campaign_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $clear_CampaignName = str_replace('/', ' ', $clear_CampaignName);

        $data->google_file_name = trim($clear_AdvertiserName) . '-' . trim($clear_CampaignName) . '-' . $data->order_number;

        $file = $request->file('file_io');
        $filePath = $file->getRealPath();
        $path = public_path($data->path_docx . $data->google_file_name . ".docx");

        $result = copy($filePath, $path);
        if(!$result){
            return redirect()->back()->withInput($request->all())->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "File has not been saved",
                'autohide' => 0,
            ]]);
        }
        @unlink($filePath);

        $result = $data->createGoogleDriveDocx();
        if($result){

            DB::beginTransaction();

            try {

                $data->save();

                if ($data->pipedrive_id) {
                    Deal::where('pd_deal_id', $data->pipedrive_id)->update(['status' => 3]);
                }
                DB::commit();

            } catch (Exception $e){

                DB::rollBack();

                return redirect()->back()->withInput($request->all())->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'autohide' => 0,
                ]]);


            } catch (PDOException $e){

                DB::rollBack();

                return redirect()->back()->withInput($request->all())->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $e->getMessage(),
                    'autohide' => 0,
                ]]);
            }

            $mailer = new Mailer();
            $mailer->sendNewIO($data);

            return redirect()->route('admin.io.individual')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Insertion order <a target='_blank' href='".$data->google_url."'>$data->google_file_name</a> has been created !",
                'autohide' => 0,
            ]]);

        } else {

            return redirect()->back()->withInput($request->all())->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Google Drive return error, please try again !",
                'autohide' => 0,
            ]]);
        }

    }

}
