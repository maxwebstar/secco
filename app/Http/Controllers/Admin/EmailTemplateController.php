<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateGroup;

use App\Services\Validator\EmailStringPlaceHolder;

class EmailTemplateController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:email_template_access'], ['only' => ['index']]);
        $this->middleware(['permission:email_template_group_access'], ['only' => ['group']]);
        $this->middleware(['permission:email_template_create_edit'], ['only' => ['add', 'edit', 'save']]);
        $this->middleware(['permission:email_template_group_create_edit'], ['only' => ['groupAdd', 'groupEdit', 'groupSave']]);
    }

    public function index()
    {
        $model = new EmailTemplate();

        $data = $model->all();

        return view('admin.email.template.index', ['data' => $data]);
    }


    public function add()
    {
        $model = new EmailTemplate();
        $modelGroup = new EmailTemplateGroup();

        $dataGroup = $modelGroup->where('show', 1)->orderBy('position')->get();

        return view('admin.email.template.add', [
            'dataGroup' => $dataGroup,
            'arrStatus' => $model->arrStatus,
            'data' => $model]);
    }


    public function edit($id)
    {
        $model = new EmailTemplate();
        $modelGroup = new EmailTemplateGroup();

        $data = $model->findOrFail($id);
        $dataGroup = $modelGroup->where('show', 1)->orderBy('position')->get();

        return view('admin.email.template.edit', ['data' => $data, 'dataGroup' => $dataGroup]);
    }

    public function save(Request $request)
    {
        $model = new EmailTemplate();

        if($request->id){

            $this->validate($request, [
                'display_name' => 'required|max:255',
                'to' => ['nullable', new EmailStringPlaceHolder()],
                'from_name' => 'required|max:255',
                'from_email' => 'required|max:255',
                'subject' => 'required|max:255',
                'body' => 'required',
                'status' => 'required|integer',
                'position' => 'required|integer',
                'description' => 'nullable',
            ]);

            $case = "updated";
            $data = $model->findOrFail($request->id);
        } else {

            $this->validate($request, [
                'group_id' => 'required|integer',
                'name' => 'required|max:63',
                'display_name' => 'required|max:255',
                'to' => ['nullable', new EmailStringPlaceHolder()],
                'from_name' => 'required|max:255',
                'from_email' => 'required|max:255',
                'subject' => 'required|max:255',
                'body' => 'required',
                'status' => 'required|integer',
                'position' => 'required|integer',
                'description' => 'nullable',
            ]);

            $case = "created";
            $data =  $model;

            $data->group_id = $request->group_id;
            $groupName = $data->template_group->name;

            if(!$groupName){
                throw new Exception('No data for template group !');
            }
            $data->name = trim($groupName . "_" . $request->name);
        }

        $data->fill([
            'display_name' => $request->display_name,
            'to' => $request->to,
            'from_name' => $request->from_name,
            'from_email' => $request->from_email,
            'subject' => $request->subject,
            'body' => $request->body,
            'status' => $request->status,
            'position' => $request->position,
            'description' => $request->description,
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

        return redirect()->route('admin.email.template')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Email template $data->display_name has been $case !",
            'autohide' => 1,
        ]]);
    }


    public function group()
    {
        $model = new EmailTemplateGroup();

        $data = $model->orderBy('position')->get();

        return view('admin.email.template.group.index', ['data' => $data]);
    }


    public function groupAdd()
    {
        return view('admin.email.template.group.add');
    }


    public function groupEdit($id)
    {
        $model = new EmailTemplateGroup();

        $data = $model->findOrFail($id);

        return view('admin.email.template.group.edit', ['data' => $data]);
    }


    public function groupSave(Request $request)
    {

        $model = new EmailTemplateGroup();

        if($request->id){

            $this->validate($request, [
                'display_name' => 'required|max:255',
                'position' => 'required|integer',
            ]);

            $case = "updated";
            $data = $model->findOrFail($request->id);
        } else {

            $this->validate($request, [
                'name' => 'required|max:63',
                'display_name' => 'required|max:255',
                'position' => 'required|integer',
            ]);

            $case = "created";
            $data =  $model;
            $data->name = trim($request->name);
        }

        $data->fill([
            'display_name' => $request->display_name,
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

        return redirect()->route('admin.email.template.group')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Template Group $data->display_name has been $case !",
            'autohide' => 1,
        ]]);
    }
}
