<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\TermTemplate;

class TermTemplateController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:term_template_access'], ['only' => ['index']]);
        $this->middleware(['permission:term_template_create_edit'], ['only' => ['add', 'edit', 'save']]);
    }


    public function index()
    {
        $model = new TermTemplate();

        $data = $model->all();

        return view('admin.term.template.index', ['data' => $data]);
    }


    public function add()
    {
        $model = new TermTemplate();

        return view('admin.term.template.add', ['data' => $model]);
    }


    public function edit($id)
    {
        $model = new TermTemplate();

        $data = $model->findOrFail($id);

        return view('admin.term.template.edit', ['data' => $data]);
    }

    public function save(Request $request)
    {
        $model = new TermTemplate();

        if($request->id){

            $this->validate($request, [
                'display_name' => 'required|max:255',
                'text' => 'nullable',
                'description' => 'nullable',
                'show' => 'nullable|integer',
                'position' => 'required|integer',
                'by_default' => 'nullable|integer',

            ]);

            $case = "updated";
            $data = $model->findOrFail($request->id);
        } else {

            $this->validate($request, [
                'display_name' => 'required|max:255',
                'text' => 'nullable',
                'description' => 'nullable',
                'show' => 'nullable|integer',
                'position' => 'required|integer',
                'by_default' => 'nullable|integer',
            ]);

            $case = "created";
            $data =  $model;
        }

        $data->fill([
            'display_name' => $request->display_name,
            'text' => $request->text,
            'description' => $request->description,
            'by_default' => $request->by_default ? 1 : 0,
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

        return redirect()->route('admin.term.template')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Term template $data->display_name has been $case !",
            'autohide' => 1,
        ]]);
    }

}
