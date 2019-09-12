<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use DB;
use App\Models\User;
use App\Models\Entrust\Role;
use Carbon\Carbon;
use PhpOffice\PhpWord\Exception\Exception;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user_access'], ['only' => ['index']]);
        $this->middleware(['permission:user_edit'], ['only' => ['view', 'update']]);
        $this->middleware(['permission:user_create'], ['only' => ['add']]);
        $this->middleware(['permission:user_delete'], ['only' => ['delete']]);
    }

    public function index()
    {
        $data = User::where('status', '!=', 0)->get();
        $auth = Auth::user();

        $permission = [
            'user_search' => $auth->can('user_search'),
        ];

        return view('admin.user.index', ['data' => $data, 'permission' => $permission]);
    }

    public function view($id)
    {
        $data = User::findOrFail($id);
        $dataRole = Role::orderBy('position')->get();

        return view('admin.user.view', ['data' => $data, 'role_id' => $data->getRoleParam(), 'dataRole' => $dataRole]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$request->id,
            'google_folder' => 'nullable|string|max:255',
            'lt_id' => 'nullable|integer',
            'ef_id' => 'nullable|required_with:show_for_manage_list|required_with:show_for_account_manage_list|integer',
            'role' => 'required|integer',
        ]);

        $data = User::findOrFail($request->id);

        $data->fill([
            'name' => $request->name,
            'email' => $request->email,
            'google_folder' => $request->google_folder,
            'lt_id' => $request->lt_id ? : 0,
            'ef_id' => $request->ef_id ? : 0,
            'show_for_manage_list' => $request->show_for_manage_list ? 1 : 0,
            'show_for_account_manage_list' => $request->show_for_account_manage_list ? 1 : 0,
            'docusign_manager' => $request->docusign_manager ? 1 : 0,
        ]);

        if($data->show_for_manage_list && $data->google_folder == false){
            $data->createGoogleDriveFolder();
        }

        DB::beginTransaction();

        try{

            $data->roles()->sync([$request->role]);
            $data->save();

            DB::commit();

        } catch (Exception $exception) {

            DB::rollBack();

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $exception->getMessage(),
                'autohide' => 0,
            ]]);
        }

        return redirect()->route('admin.user')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "User $request->email has been updated",
            'autohide' => 1,
        ]]);
    }


    public function add(Request $request)
    {
        $dataRole = Role::orderBy('position')->get();

        if($request->isMethod('post')){

            $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'google_folder' => 'nullable|string|max:255',
                'lt_id' => 'nullable|integer',
                'ef_id' => 'nullable|required_with:show_for_manage_list|required_with:show_for_account_manage_list|integer',
                'role' => 'required|integer'
            ]);

            $password = str_random(10);

            $dataUser = new User();
            $dataUser->fill([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $password,
                'google_folder' => $request->google_folder,
                'lt_id' => $request->lt_id ? : 0,
                'ef_id' => $request->ef_id ? : 0,
                'show_for_manage_list' => $request->show_for_manage_list ? 1 : 0,
                'show_for_account_manage_list' => $request->show_for_account_manage_list ? 1 : 0,
                'docusign_manager' => $request->docusign_manager ? 1 : 0,
                'status' => 3,
            ]);

            if($dataUser->show_for_manage_list && $dataUser->google_folder == false){
                $dataUser->createGoogleDriveFolder();
            }

            DB::beginTransaction();

            try{

                $dataUser->save();
                $dataUser->roles()->sync([$request->role]);

                DB::commit();

            } catch (Exception $exception) {

                DB::rollBack();

                return redirect()->back()->with(['message' => [
                    'type' => 'danger',
                    'title' => 'Error!',
                    'message' => $exception->getMessage(),
                    'autohide' => 0,
                ]]);
            }

            return redirect()->route('admin.user')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "User $request->email has been created",
                'autohide' => 1,
            ]]);
        }

        return view('admin.user.add', ['dataRole' => $dataRole]);
    }


    public function profile(Request $request)
    {
        $dataUser = Auth::user();

        if($request->isMethod('post')){

            $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email,'.$dataUser->id,
                'password' => 'nullable|string|min:6',
                'password_new' => 'nullable|required_with:password|string|min:6|confirmed',
            ]);

            if($request->password && $request->password_new) {
                if (Hash::check($request->password, $dataUser->password) && $dataUser->can('user_change_password')) {
                    $dataUser->password = Hash::make($request->password_new);
                } else {
                    return redirect()->route('admin.profile')->with(['message' => [
                        'type' => 'warning',
                        'title' => 'Warning!',
                        'message' => "The entered password did not match or you do not have permission. Data was not updated.",
                        'autohide' => 0,
                    ]]);
                }
            }

            $dataUser->fill([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $dataUser->save();

            return redirect()->route('admin.profile')->with(['message' => [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Your profile has been updated",
                'autohide' => 1,
            ]]);
        }

        return view('admin.user.profile', ['data' => $dataUser]);
    }


    public function approve($id)
    {
        try{

            $data = User::findOrFail($id);
            $data->fill([
                'status' => 3,
                'date_approve' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
            $data->save();

        } catch(Exception $exception) {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $exception->getMessage(),
                'autohide' => 0,
            ]]);
        }

        return redirect()->back()->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "User has been approved",
            'autohide' => 1,
        ]]);
    }

    public function reject($id)
    {
        try{

            $data = User::findOrFail($id);
            $data->fill([
                'status' => 2,
                'date_reject' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
            $data->save();

        } catch(Exception $exception) {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $exception->getMessage(),
                'autohide' => 0,
            ]]);

        }

        return redirect()->back()->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "User has been rejected",
            'autohide' => 1,
        ]]);
    }


    public function delete($id)
    {
        $data = User::findOrFail($id);
        $email = $data->email;

        try {

            if ($data->status == 1) {
                $data->delete();
            } else {
                $data->status = 0;
                $data->save();
            }

        } catch (Exception $e) {

            return redirect()->route('admin.user')->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'autohide' => 0,
            ]]);
        }

        return redirect()->route('admin.user')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "User $email has been removed.",
            'autohide' => 1,
        ]]);

    }
}
