<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'request_statistic';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['advertiser_id', 'from_user_id', 'advertiser_contact', 'advertiser_email', 'notification', 'reason', 'updated_by_id', 'created_by_id'];

    /**
     * Get advertiser.
     *
     * @var Eloquent
     */
    public function advertiser()
    {
        return $this->hasOne('App\Models\Advertiser', 'id', 'advertiser_id')->withDefault([
            'name' => '',
            'contact' => '',
            'email' => '',
            'billing_email' => '',
            'lt_id' => 0,
            'ef_id' => 0,
            'ef_status' => '',
        ]);
    }

    /**
     * Get from user.
     *
     * @var Eloquent
     */
    public function from_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'from_user_id');
    }

    /**
     * Get author.
     *
     * @var Eloquent
     */
    public function updated_param()
    {
        return $this->hasOne('App\Models\User', 'id', 'updated_by_id');
    }

    /**
     * Get author.
     *
     * @var Eloquent
     */
    public function created_param()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by_id');
    }


}
