<?php

namespace App\Services\Validator;

use Illuminate\Contracts\Validation\Rule;

class OneRequiredArray implements Rule
{

    protected $message = '';

    public function passes($attribute, $value)
    {
        $fieldEnum = "";

        if($value && is_array($value)){
            foreach($value as $field => $iter) {

                $iter = trim($iter);
                if($iter) {
                    return true;
                }
                $fieldEnum .= $field . ', ';
            }
        }

        $fieldEnum = $fieldEnum ? substr($fieldEnum, 0, -2) : $fieldEnum;

        $this->message .= "At least one of the fields listed above [$fieldEnum] must be filled";

        return false;
    }


    public function message()
    {
        return $this->message;
    }

}