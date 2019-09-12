<?php

namespace App\Http\Controllers\Admin\QB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\User as modelUser;
use App\Models\Network as modelNetwork;
use App\Models\QB\Customer as modelQBCustomer;

use DB;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:permission_service_access'], ['only' => ['index', 'add', 'edit', 'save']]);
    }

    public function index()
    {
        $modelUser = new modelUser();

        $dataManager = $modelUser->getManager();

        return view('admin.qb.customer.index', [
            'auth' => Auth::user(),
            'data' => new modelQBCustomer(),
            'dataManager' => $dataManager
        ]);
    }


    public function ajaxGet(Request $request)
    {

        $columns     = ['c.quickbook_id', 'c.name', 'c.email', 'c.phone', 'c.company', 'a.name', 'c.active', 'c.status', 'c.created_qb', 'c.id'];
        $columnsLike = ['c.quickbook_id', 'c.name', 'c.email', 'c.phone', 'c.company', 'a.name', 'c.active', 'c.status', DB::raw('DATE_FORMAT(c.created_qb, "%b %e, %Y %T")'), 'c.id'];

        $draw        = $request->draw;
        $start       = $request->start; //Start is the offset
        $length      = $request->length; //How many records to show
        $order       = $request->order[0]['column']; //Order by column
        $dir         = $request->order[0]['dir']; //Direction of orderBy
        $searchValue = $request->search['value']; //Search value


        $sqlDB = DB::connection('mysql');
        $query = $sqlDB->table('qb_customer as c')
            ->select('c.quickbook_id', 'c.name', 'c.email', 'c.phone', 'c.company', DB::raw('a.name AS advertiser_name'), 'c.active', 'c.status', DB::raw('DATE_FORMAT(c.created_qb, "%b %e, %Y %T") AS created_qb'), 'c.id')
            ->leftJoin('advertiser AS a', 'a.id', '=', 'c.advertiser_id')
            ->offset($start)
            ->limit($length)
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

        if($request->manager){
            $query->where('a.manager_id', $request->manager);
        }
        if($request->status){
            $query->where('c.status', $request->status);
        }
        if($request->active){
            $query->where('c.active', 1);
        }

        $data = $query->get();
        $json_data = array(
            "draw"            => intval($draw),
            "recordsTotal"    => $sqlDB->table('qb_customer')->count(),
            "recordsFiltered" => $query->count(),
            "data"            => $data,
            'sql'             => $query->toSql(),
            'search' => "%$searchValue%",
        );

        echo json_encode($json_data);
    }


    public function view($id)
    {
        $data = modelQBCustomer::where('id', $id)->first();
        if($data){

            return view('admin.qb.customer.view', [
                'data' => $data,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Customer not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function attache($id)
    {
        $data = modelQBCustomer::where('id', $id)->whereIn('status', [1, 2])->first();
        if($data){

            $modelNetwork = new modelNetwork();
            $dataNetwork = $modelNetwork->getNetwork();

            $dataAdvertiser = $data->advertiser;
            if($dataAdvertiser->name){
                $selectNetwork = $modelNetwork->find($data->advertiser_network_id);
                $labelAdvertiser = "(" . $dataAdvertiser->{$selectNetwork->field_name} . ") " .  $dataAdvertiser->name;
            } else {
                $labelAdvertiser = "";
            }

            return view('admin.qb.customer.attache', [
                'data' => $data,
                'dataNetwork' => $dataNetwork,
                'labelAdvertiser' => $labelAdvertiser,
            ]);

        } else {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Customer not fount, please try again !",
                'autohide' => 0,
            ]]);
        }
    }


    public function saveAttache(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'network_id' => 'required|integer',
            'advertiser_id' => [
                'required',
                'integer',
                Rule::unique('qb_customer')->ignore($request->id, 'id'),
            ]
        ]);

        $modelNetwork = new modelNetwork();
        $dataNetwork = $modelNetwork->getNetwork();

        $data = modelQBCustomer::where('id', $request->id)->whereIn('status', [1, 2])->first();
        if($data){

            $data->advertiser_network_id = $request->network_id;
            $data->advertiser_id = $request->advertiser_id;
            $data->status = 2;
            $data->save();

            $dataAdvertiser = $data->advertiser;
            if($dataAdvertiser->name){
                $selectNetwork = $modelNetwork->find($data->advertiser_network_id);
                $labelAdvertiser = "(" . $dataAdvertiser->{$selectNetwork->field_name} . ") " .  $dataAdvertiser->name;
            } else {
                $labelAdvertiser = "";
            }

            $alert = [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Advertiser has been attached to customer.",
                'hide' => 1,
            ];

            return view('admin.qb.customer.attache', [
                'status' => 'success',
                'alert' => $alert,
                'data' => $data,
                'dataNetwork' => $dataNetwork,
                'labelAdvertiser' => $labelAdvertiser,
            ]);

        } else {

            $labelAdvertiser = "";

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Customer not fount, please try again !",
                'hide' => 0,
            ];

            return view('admin.qb.customer.attache', [
                'status' => 'error',
                'alert' => $alert,
                'data' => $data,
                'dataNetwork' => $dataNetwork,
                'labelAdvertiser' => $labelAdvertiser,
            ]);
        }
    }

}
