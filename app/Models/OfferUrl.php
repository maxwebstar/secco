<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferUrl extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offer_url';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'offer_id', 'name', 'url', 'ef_id', 'ef_status' ,'created_at', 'updated_at'];

}
