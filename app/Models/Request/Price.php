<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'request_price';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['network_id', 'offer_id', 'affiliate_all', 'affiliate_id',
        'date', 'price_in', 'price_out', 'current_price_in', 'current_price_out',
        'type', 'cap_change', 'reason', 'status', 'is_fx_rate', 'ef_id', 'error_api',
        'created_by', 'created_by_id', 'updated_at', 'created_at', 'mongo_id'];

    /**
     * Status for price
     *
     * 1 - New
     * 2 - Declined
     * 3 - Approved
     */

    public $arrStatus = [
        1 => "New",
        2 => "Declined",
        3 => "Approved",
    ];

    /**
     * Type for price
     *
     * 1 - Increase
     * 2 - Decrease
     */

    public $arrType = [
        1 => "Increase",
        2 => "Decrease",
    ];

    public function getType()
    {
        return isset($this->arrType[$this->type]) ? $this->arrType[$this->type] : "None";
    }

    public function getStatus()
    {
        return isset($this->arrStatus[$this->status]) ? $this->arrStatus[$this->status] : "None";
    }


    /**
     * Get network.
     *
     * @var Eloquent
     */
    public function network()
    {
        return $this->hasOne('App\Models\Network', 'id', 'network_id');
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
            'ef_status' => '',
            'campaign_name' => '',
        ]);
    }

    /**
     * Get Affiliate.
     *
     * @var Eloquent
     */
    public function affiliate()
    {
        return $this->hasOne('App\Models\Affiliate', 'id', 'affiliate_id')->withDefault([
            'name' => '',
            'lt_id' => 0,
            'ef_id' => 0,
            'lt_status' => '',
            'ef_status' => '',
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
