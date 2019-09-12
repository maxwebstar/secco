<?php

namespace App\Services\Validator;

use Illuminate\Contracts\Validation\Rule;

class EmailStringPlaceHolder implements Rule
{

    protected $message = '';

    protected $placeholder = [
        '[Author]',
        '[Sales Manager]',
        '[Account Manager]'
    ];


    public function passes($attribute, $value)
    {
        $result = true;

        if($value){

            $arr = explode(",", $value);
            foreach($arr as $email) {

                $email = trim($email);
                if($email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {

                        if(in_array($email, $this->placeholder) == false){

                            $this->message .= "Email address <font color='blue'>'$email'</font> is invalid.<br>";
                            $result = false;
                        }
                    }
                }
            }
        }

        return $result;
    }


    public function message()
    {
        return $this->message;
    }

}