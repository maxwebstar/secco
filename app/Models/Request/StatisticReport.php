<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StatisticReport extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'request_statistic_report';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['advertiser_id', 'from_user_id', 'subject', 'body', 'date', 'error', 'status'];


//    protected $casts = [
//        'date'  => 'date:Y-m',
//    ];
//
//
//    public function setDateAttribute( $value ) {
//        $this->attributes['date'] = (new Carbon($value))->format('Y-m');
//    }

}
