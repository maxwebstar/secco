<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_template';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_id', 'name', 'display_name', 'to', 'from_name', 'from_email', 'subject', 'body', 'status', 'position', 'description'];

    /**
     * Get template group.
     *
     * @var Eloquent
     */
    public function template_group()
    {
        return $this->hasOne('App\Models\EmailTemplateGroup', 'id', 'group_id');
    }

    /**
     * Status for email template
     *
     * 1 - need correction
     * 2 - not send this email
     * 3 - send this email
     */

    public $arrStatus = [
        1 => "Need correction",
        2 => "Not send email",
        3 => "Send email"
    ];

    public function getStatus()
    {
        return isset($this->arrStatus[$this->status]) ? $this->arrStatus[$this->status] : "None";
    }



    public function getTo($type = "array")
    {
        $result = $this->to;
        if($this->to){

            $result = array();
            $arr = explode(",", $this->to);

            foreach($arr as $email){
                $result[] = trim($email);
            }

            if($type == "string"){
                $str = implode("<br>", $result);
                $result = $str;
            }
        }

        return $result;
    }


    public function getDefault($filed = "description")
    {
        $arr = [
            "description" => "Trigger:\nIndicator on Dash:\nDistribution:\nOther:"
        ];

        return isset($arr[$filed]) ? $arr[$filed] : "";
    }


    public function getDescription()
    {
        return $this->description ? str_replace("\n", "<br>", $this->description) : $this->description;
    }
}
