<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class AdvertiserStat extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advertiser_stat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['advertiser_id', 'lt_id', 'ef_id', 'network_id', 'date', 'approved', 'click', 'revenue', 'payout', 'profit'];

    /**
     * Get network.
     *
     * @var Eloquent
     */
    public function network()
    {
        return $this->hasOne('App\Models\Network', 'network_id', 'network_id');
    }


    public function getDataByPrevMonth($advertiser_id)
    {
        /*
        $dateStart = date("Y-m-02", strtotime("- 1 month"));
        $dateEnd = date("Y-m-01");
        */

        $date = date("Y-m", strtotime("- 1 month"));

        $sqlDB = DB::connection('mysql');

        $dataStat = $sqlDB->table('advertiser_stat')
            ->select(DB::raw('SUM(approved) as approved'), DB::raw('SUM(click) as click'), DB::raw('SUM(revenue) as	revenue'), DB::raw('SUM(payout) as payout'), DB::raw('SUM(profit) as profit'))
            ->where('advertiser_id', $advertiser_id)
            ->where(DB::raw('DATE_FORMAT(date, "%Y-%m")'), '>=', $date)
            ->first();

        return $dataStat;
    }


    public function getDataByCurrentMonth($advertiser_id)
    {
        /*
        if(date('j') == 1){
            $dateStart = date("Y-m-02", strtotime("- 1 month"));
            $dateEnd = date("Y-m-01");
        } else {
            $dateStart = date("Y-m-02");
            $dateEnd = date("Y-m-d");
        }*/

        $dateStart = date("Y-m-01");
        $dateEnd = date("Y-m-d");

        $sqlDB = DB::connection('mysql');

        $dataStat = $sqlDB->table('advertiser_stat')
            ->select(DB::raw('SUM(approved) as approved'), DB::raw('SUM(click) as click'), DB::raw('SUM(revenue) as	revenue'), DB::raw('SUM(payout) as payout'), DB::raw('SUM(profit) as profit'))
            ->where('advertiser_id', $advertiser_id)
            ->where('date', '>=', $dateStart)
            ->where('date', '<=', $dateEnd)
            ->first();

        return $dataStat;
    }


    public function getData($advertiser_id)
    {
        $sqlDB = DB::connection('mysql');

        $dataStat = $sqlDB->table('advertiser_stat')
            ->select(DB::raw('SUM(approved) as approved'), DB::raw('SUM(click) as click'), DB::raw('SUM(revenue) as	revenue'), DB::raw('SUM(payout) as payout'), DB::raw('SUM(profit) as profit'))
            ->where('advertiser_id', $advertiser_id)
            ->first();

        return $dataStat;
    }
}
