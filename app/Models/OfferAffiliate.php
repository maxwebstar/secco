<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferAffiliate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offer_affiliate';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['network_id', 'offer_id', 'affiliate_id', 'network_offer_id', 'network_affiliate_id'];
}
