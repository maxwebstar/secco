<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class TestController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function index()
    {
//        var_dump(bcrypt('connect123NEW'));
//
//        $hash = '$2y$10$arzXyBNGezhjPFrkivRPzOCKBG3IRFb6vdNIQGE2eSPgbBsTTKPMS';
//        $hash_old = '$2a$12$Jg4D0./hjtyAbLkPPE742OJWTqhgLXAYV4pjDjskpqn3VbnH5CZfW';
//
//        if(password_verify('connect123NEW', $hash_old)){
//            echo "ok";
//        } else {
//            echo "not";
//        }

        //"$2y$10$arzXyBNGezhjPFrkivRPzOCKBG3IRFb6vdNIQGE2eSPgbBsTTKPMS";
        //"$2a$12$Jg4D0./hjtyAbLkPPE742OJWTqhgLXAYV4pjDjskpqn3VbnH5CZfW";


        //return abort(500);

//        $string = "test@gmail.com";
//        $string = " ";
//        $arr = explode(",", $string);
//
//        var_dump($arr);

//        return redirect()->route('home')->with(['message' => [
//            'type' => 'success',
//            'title' => 'Success!',
//            'message' => "Congratulations, your account has been created. Please wait while admin checks your account.",
//            'autohide' => 1,
//        ]]);

        phpinfo();
    }
}
