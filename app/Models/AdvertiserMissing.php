<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserMissing extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advertiser_missing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'contact', 'email', 'country', 'state', 'city', 'street1', 'zip',
        'currency_id',	'manager_id', 'manager_account_id',	'lt_id', 'ef_id', 'ef_status', 'status', 'is_duplicate', 'updated_by_id'
    ];

    /**
     * Status
     *
     * 1 - New
     * 2 - Declined
     * 3 - Approved
     */

    public $arrStatus = [
        1 => "New",
        2 => "Ignored",
        3 => "Added",
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
    public function manager()
    {
        return $this->hasOne('App\Models\User', 'id', 'manager_id')->withDefault([
            'name' => ''
        ]);
    }

    /**
     * Get manager sale.
     *
     * @var Eloquent
     */
    public function manager_account()
    {
        return $this->hasOne('App\Models\User', 'id', 'manager_account_id')->withDefault([
            'name' => '',
        ]);
    }

    /**
     * Get country.
     *
     * @var Eloquent
     */
    public function country_param()
    {
        return $this->hasOne('App\Models\Country', 'key', 'country')->withDefault([
            'name' => ''
        ]);
    }

    /**
     * Get state.
     *
     * @var Eloquent
     */
    public function state_param()
    {
        return $this->hasOne('App\Models\State', 'key', 'state')->withDefault([
            'name' => ''
        ]);
    }

    /**
     * Get currency.
     *
     * @var Eloquent
     */
    public function currency()
    {
        return $this->hasOne('App\Models\Currency', 'id', 'currency_id')->withDefault([
            'name' => ''
        ]);
    }
}
