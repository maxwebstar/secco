<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'request_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['offer_id', 'date', 'need_api_lt', 'need_api_ef', 'lt_status', 'ef_status', 'mass_notice', 'redirect_url', 'reason', 'status', 'error_cron', 'created_by', 'created_by_id', 'updated_at', 'created_at', 'mongo_id'];

    /**
     * Status for io
     *
     * 1 - New
     * 2 - Declined
     * 3 - Approved
     */

    public $arrStatus = [
        1 => "New",
        2 => "Declined",
        3 => "Approved",
        4 => "Wait Cron",
    ];

    public function getStatus()
    {
        return isset($this->arrStatus[$this->status]) ? $this->arrStatus[$this->status] : "None";
    }

    /**
     * Get Offer.
     *
     * @var Eloquent
     */
    public function offer()
    {
        return $this->hasOne('App\Models\Offer', 'id', 'offer_id')->withDefault([
            'need_api_lt' => 0,
            'need_api_ef' => 0,
            'lt_id' => 0,
            'ef_id' => 0,
            'lt_status' => '',
            'ef_status' => '',
            'campaign_name' => '',
        ]);
    }


    /**
     * Get author.
     *
     * @var Eloquent
     */
    public function created_param()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by_id')->withDefault([
            'name' => $this->created_by,
            'email' => '',
        ]);
    }
}
