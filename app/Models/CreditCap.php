<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCap extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'credit_cap';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['advertiser_id',	'revenue', 'revenue_mtd', 'balance', 'cap', 'cap_percent', 'cap_type', 'ar', 'is_6_month', 'num_month', 'notify_limit'];

    /**
     * Type for cap
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
        return isset($this->arrType[$this->cap_type]) ? $this->arrType[$this->cap_type] : "None";
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
