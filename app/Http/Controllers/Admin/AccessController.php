<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;

use App\Models\Access as modelAccess;
use App\Models\User as modelUser;

use DB;
use Exception;
use PDOException;
use Redirect;

class AccessController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:permission_service_access'], ['only' => ['index', 'add', 'edit', 'save']]);
    }


    public function index()
    {
        $this->checkAccess();

        $data = modelAccess::orderBy('position', 'ASC')->get();

        return view('admin.access.index', [
            'data' => $data,
        ]);
    }


    public function add()
    {
        $this->checkAccess();

        return view('admin.access.add', []);
    }


    public function edit($id)
    {
        $this->checkAccess();

        $data = modelAccess::where('id', $id)->first();
        if(!$data){

            return redirect()->route('admin.access.edit')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => "Data not fount, please try again !",
                'autohide' => 0,
            ]]);
        }

        return view('admin.access.edit', [
            'data' => $data,
        ]);
    }


    public function save(Request $request)
    {
        $this->checkAccess();

        if($request->id){

            $this->validate($request, [
                /*'name' => 'required|max:100|alpha_num|unique:access,name,'.$request->id,*/
                'label' => 'required|max:100|string',
                'value' => 'required|max:255|string',
                'position' => 'required|integer',
                'show' => 'required|integer',
            ]);

            $data = modelAccess::findOrFail($request->id);

        } else {

            $this->validate($request, [
                'name' => 'required|max:100|string|unique:access',
                'label' => 'required|max:100|string',
                'value' => 'required|max:255|string',
                'position' => 'required|integer',
                'show' => 'required|integer',
            ]);

            $data = new modelAccess();
            $data->name = $request->name;
        }

        $data->fill([
            'value' => $request->value,
            'label' => $request->label,
            'position' => $request->position,
            'show' => $request->show,
        ]);

        $data->save();

        return redirect()->route('admin.access.index')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Data has been saved !",
            'autohide' => 1,
        ]]);
    }


    public function login()
    {
        return view('admin.access.login', []);
    }


    public function loginCheck(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|max:255|string',
        ]);

        if($request->password == "7secret4access8"){

            session()->put('page_access_auth', 'ok');

            return redirect()->route('admin.access.index');

        } else {

            throw ValidationException::withMessages([
                'password' => ["These credentials do not match our records."],
            ]);
        }
    }


    public function manage()
    {

    }


    public function checkAccess()
    {
        if(session()->get('page_access_auth') != "ok"){

            Redirect::to('/admin/access/login')->send();
        }
    }


}