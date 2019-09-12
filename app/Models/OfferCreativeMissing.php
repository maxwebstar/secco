<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferCreativeMissing extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offer_creative_missing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['offer_id', 'creative_id', 'name', 'link', 'price_in', 'price_out', 'lt_id', 'ef_id', 'ef_status', 'status', 'updated_by_id', 'updated_by', 'created_at', 'updated_at'];

    /**
     * Status for io
     *
     * 1 - New
     * 2 - Declined
     * 3 - Approved
     */

    public $arrStatus = [
        1 => "New",
        2 => "Ignored",
        3 => "Added",
        4 => "Attached",
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
        return $this->hasOne('App\Models\Offer', 'id', 'offer_id');
    }


    /**
     * Get Creative.
     *
     * @var Eloquent
     */
    public function creative()
    {
        return $this->hasOne('App\Models\OfferCreative', 'id', 'creative_id')->withDefault([
            'name' => '',
        ]);
    }
}
