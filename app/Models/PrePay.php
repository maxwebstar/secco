<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrePay extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pre_pay';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['advertiser_id', 'amount', 'revenue', 'revenue_mtd', 'ar', 'balance_remaining', 'used_percent', 'type', 'created_at', 'updated_at', 'notify_limit'];


    /**
     * Type for prepay
     *
     * 0 - none
     * 1 - manual
     * 2 - auto
     */

    public $arrType = [
        0 => "None",
        1 => "Manual",
        2 => "Auto",
    ];

    public function getType()
    {
        return isset($this->arrType[$this->type]) ? $this->arrType[$this->type] : "None";
    }

    /**
     * Get advertiser.
     *
     * @var Eloquent
     */
    public function advertiser()
    {
        return $this->hasOne('App\Models\Advertiser', 'id', 'advertiser_id');
    }
}
