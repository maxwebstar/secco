<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Domain;

class DomainController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:domain_access'], ['only' => ['index']]);
        $this->middleware(['permission:domain_create'], ['only' => ['add', 'save']]);
        $this->middleware(['permission:domain_edit'], ['only' => ['edit', 'save']]);
    }

    public function index()
    {
        $data = Domain::all();

        return view('admin.domain.index', ['data' => $data]);
    }

    public function add()
    {
        return view('admin.domain.add');
    }

    public function edit($id)
    {
        $data = Domain::findOrFail($id);

        return view('admin.domain.edit', ['data' => $data]);
    }

    public function save(Request $request)
    {
        $model = new Domain();

        if($request->id){

            $this->validate($request, [
                'value' => 'required|max:63|alpha_num|unique:domain,value,'.$request->id,
                'name' => 'required|max:63|alpha_num',
                'position' => 'required|integer',
            ]);

            $case = "updated";
            $data = $model->findOrFail($request->id);

        } else {

            $this->validate($request, [
                'value' => 'required|max:63|alpha_num|unique:domain,value',
                'name' => 'required|max:63|alpha_num',
                'position' => 'required|integer',
            ]);

            $case = "created";
            $data =  $model;
        }

        $data->fill([
            'value' => strtolower($request->value),
            'name' => $request->name,
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

        return redirect()->route('admin.domain')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Permission $data->name has been $case !",
            'autohide' => 1,
        ]]);
    }
}
