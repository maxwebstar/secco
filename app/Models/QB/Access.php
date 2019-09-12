<?php

namespace App\Models\QB;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'qb_access';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['real_m_id',	'access_token',	'refresh_token', 'expires_in', 'refresh_token_expires_in'];
}