<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferCreative extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offer_creative';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['offer_id', 'request_id', 'iteration', 'name', 'link', 'price_in', 'price_out', 'lt_id', 'ef_id', 'ef_status', 'status', 'created_at', 'updated_at'];

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
    ];

    public function getStatus()
    {
        return isset($this->arrStatus[$this->status]) ? $this->arrStatus[$this->status] : "None";
    }

}
