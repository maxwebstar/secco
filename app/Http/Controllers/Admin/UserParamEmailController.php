<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\User as modelUser;
use App\Models\UserParamEmail as modelUserParamEmail;

use DB;
use Exception;
use PDOException;

class UserParamEmailController extends Controller
{
    public function edit()
    {
        $auth = Auth::user();

        $data = modelUserParamEmail::firstOrNew(['user_id' => $auth->id]);

        return view('admin.userparamemail.edit', ['data' => $data, 'auth' => $auth]);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'id' => 'nullable|integer',
            'driver' => 'required|max:63',
            'host' => 'required|max:63',
            'port' => 'required|max:63',
            'username' => 'required|max:255',
            'password' => 'required|max:255|confirmed',
            'encryption' => 'required|max:63',
        ]);

        $auth = Auth::user();

        if($request->id){
            $data = modelUserParamEmail::where('id', $request->id)->first();
        } else {
            $data = new modelUserParamEmail();
            $data->user_id = $auth->id;
        }

        $data->fill([
            'driver' => $request->driver,
            'host' => $request->host,
            'port' => $request->port,
            'username' => $request->username,
            'password' => $request->password,
            'encryption' => $request->encryption,
        ]);

        try {

            $data->save();

        } catch (Exception $e) {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'autohide' => 0,
            ]]);

        } catch (PDOException $e) {

            return redirect()->back()->with(['message' => [
                'type' => 'danger',
                'title' => 'Error!',
                'message' => $e->getMessage(),
                'autohide' => 0,
            ]]);
        }

        return redirect()->route('admin.userparamemail.edit')->with(['message' => [
            'type' => 'success',
            'title' => 'Success!',
            'message' => "Email params for $auth->name has been saved !",
            'autohide' => 0,
        ]]);
    }
}
