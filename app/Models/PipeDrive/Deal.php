<?php

namespace App\Models\PipeDrive;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pipe_drive_deal';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pd_deal_id', 'pd_organization_id', 'pd_person_id', 'pd_user_id', 'io_campaign_name', 'currency_id',
        'advertiser_name', 'advertiser_contact', 'advertiser_country', 'advertiser_street1', 'advertiser_zip', 'advertiser_email', 'advertiser_phone',
        'manager_id', 'request_body', 'status'
    ];

    /**
     * Status for deal
     *
     * 0 - delete
     * 1 - pending
     * 3 - added
     */

    public $arrStatus = [
        0 => "Delete",
        1 => "Pending",
        3 => "Added"
    ];

    public function getStatus()
    {
        return isset($this->arrStatus[$this->status]) ? $this->arrStatus[$this->status] : "None";
    }


}
