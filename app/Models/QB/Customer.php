<?php

namespace App\Models\QB;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'qb_customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['advertiser_id',	'quickbook_id', 'ar', 'name', 'email', 'phone', 'company', 'active', 'created_qb', 'status'];

    /**
     * Status for report
     *
     * 1 - not attached
     * 2 - attached to advertiser
     */

    public $arrStatus = [
        1 => "Not Attached",
        2 => "Attached to Advertiser",
    ];

    public function getStatus()
    {
        return isset($this->arrStatus[$this->status]) ? $this->arrStatus[$this->status] : "None";
    }

    /**
     * Get manager.
     *
     * @var Eloquent
     */
    public function advertiser()
    {
        return $this->hasOne('App\Models\Advertiser', 'id', 'advertiser_id')->withDefault([
            'name' => '',
        ]);
    }
}
