<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserParamEmail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_param_email';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'driver',	'host',	'port',	'username',	'password',	'encryption'];

    /**
     * Get user.
     *
     * @var Eloquent
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->withDefault([
            'name' => ''
        ]);
    }
}
