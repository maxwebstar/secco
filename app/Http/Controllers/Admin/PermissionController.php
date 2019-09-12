<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Entrust\Role;
use App\Models\Entrust\Permission;
use App\Models\Entrust\PermissionGroup;

use PhpOffice\PhpWord\Exception\Exception;
use Validator;

class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:permission_access']);
    }


    public function manage(Request $request)
    {
        $role_id = 0;

        $modelRole = new Role();
        $modelGroup = new PermissionGroup();

        $dataRole = $modelRole->orderBy('position')->get();
        $dataGroup = $modelGroup->where('show', 1)->orderBy('position')->get();
        $dataRolePermission = [];

        if($request->isMethod('post')){

            $this->validate($request, [
                'role_id' => 'required|integer',
            ]);

            $role_id = $request->role_id;
            $role = $modelRole->findOrFail($role_id);

            $tmpRolePermission = $role->perms;
            if($tmpRolePermission->isNotEmpty()){
                foreach($tmpRolePermission as $iter){
                    $dataRolePermission[$role_id][$iter->id] = 1;
                }
            }
        }

        return view('admin.permission.manage', [
            'dataRole' => $dataRole,
            'dataGroup' => $dataGroup,
            'dataRolePermission' => $dataRolePermission,
            'role_id' => $role_id]);
    }


    public function manageSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer',
            'permission' => 'required|array',
            'permission.*' => 'numeric',
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'not_valid', 'param' => $validator->errors()]);
        }

        try {

            $modelRole = new Role();
            $dataRole = $modelRole->findOrFail($request->role_id);

            $dataRole->perms()->sync(array_keys($request->permission));

            $alert = [
                'type' => 'success',
                'title' => 'Success!',
                'message' => "Permissions for $dataRole->display_name has updated !",
                'hide' => 1,
            ];

        } catch (Exception $e) {

            $alert = [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'hide' => 0,
            ];
        }

        return response()->json(['status' => 'saved', 'alert' => $alert]);

    }


    public function index()
    {
        $model = new Permission();

        $data = $model->all();

        return view('admin.permission.index', ['data' => $data]);
    }


    public function add()
    {
        $modelGroup = new PermissionGroup();

        $dataGroup = $modelGroup->where('show', 1)->orderBy('position')->get();

        return view('admin.permission.add', ['dataGroup' => $dataGroup]);
    }


    public function edit($id)
    {
        $model = new Permission();
        $modelGroup = new PermissionGroup();

        $data = $model->findOrFail($id);
        $dataGroup = $modelGroup->where('show', 1)->orderBy('position')->get();

        return view('admin.permission.edit', ['data' => $data, 'dataGroup' => $dataGroup]);
    }


    public function save(Request $request)
    {
        $model = new Permission();

        if($request->id){

            $this->validate($request, [
                'display_name' => 'required|max:63',
                'description' => 'max:255',
                'position' => 'required|integer',
            ]);

            $case = "updated";
            $data = $model->findOrFail($request->id);
        } else {

            $this->validate($request, [
                'group_id' => 'required|integer',
                'name' => 'required|max:63',
                'display_name' => 'required|max:63',
                'description' => 'max:255',
                'position' => 'required|integer',
            ]);

            $case = "created";
            $data =  $model;

            $data->group_id = $request->group_id;
            $groupName = $data->permissin_group->name;

            if(!$groupName){
                throw new Exception('No data for permissions group !');
            }
            $data->name = $groupName . "_" . $request->name;
        }

        $data->fill([
            'display_name' => $request->display_name,
            'description' => $request->description,
            'position' => $request->position,
            'show' => $request->show ? 1 : 0,
        ]);

        try{

            $data->save();

        } catch(Exception $exception) {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $exception->getMessage(),
                'autohide' => 0,
            ]]);
        }

        return redirect()->route('admin.permission')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Permission $data->display_name has been $case !",
            'autohide' => 1,
        ]]);
    }


    public function group()
    {
        $model = new PermissionGroup();

        $data = $model->orderBy('position')->get();

        return view('admin.permission.group', ['data' => $data]);
    }


    public function groupAdd()
    {
        return view('admin.permission.group.add');
    }


    public function groupEdit($id)
    {
        $model = new PermissionGroup();

        $data = $model->findOrFail($id);

        return view('admin.permission.group.edit', ['data' => $data]);
    }


    public function groupSave(Request $request)
    {
        $model = new PermissionGroup();

        if($request->id){

            $this->validate($request, [
                'display_name' => 'required|max:63',
                'description' => 'max:255',
                'position' => 'required|integer',
            ]);

            $case = "updated";
            $data = $model->findOrFail($request->id);
        } else {

            $this->validate($request, [
                'name' => 'required|max:63',
                'display_name' => 'required|max:63',
                'description' => 'max:255',
                'position' => 'required|integer',
            ]);

            $case = "created";
            $data =  $model;
            $data->name = $request->name;
        }

        $data->fill([
            'display_name' => $request->display_name,
            'description' => $request->description,
            'position' => $request->position,
            'show' => $request->show ? 1 : 0,
        ]);

        try{

            $data->save();

        } catch(Exception $exception) {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $exception->getMessage(),
                'autohide' => 0,
            ]]);
        }

        return redirect()->route('admin.permission.group')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Permission Group $data->display_name has been $case !",
            'autohide' => 1,
        ]]);

    }
}
