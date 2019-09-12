<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'affiliate';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'manager_id', 'email', 'contact',
        'country_id',	'state_id',	'city',	'street1', 'street2', 'zip', 'phone',
        'im_network',	'im_address',
        'lt_id', 'ef_id',	'lt_status', 'ef_status',
        'last_login',	'updated_by', 'updated_by_id', 'created_at', 'updated_at', 'mongo_id'];
}
