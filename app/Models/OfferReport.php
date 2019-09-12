<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class OfferReport extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offer_report';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'network_id', 'offer_network_id', 'date', 'imp', 'total_click', 'unique_click', 'approved', 'revenue', 'payout', 'profit', 'margin', 'offer_id', 'lt_id', 'ef_id', 'created_at', 'updated_at'];


    public function getData($offer_id)
    {
        $sqlDB = DB::connection('mysql');

        $dataStat = $sqlDB->table('offer_report')
            ->select(DB::raw('SUM(approved) as approved'), DB::raw('SUM(total_click) as click'), DB::raw('SUM(unique_click) as unique_click'), DB::raw('SUM(revenue) as revenue'), DB::raw('SUM(payout) as payout'), DB::raw('SUM(profit) as profit'))
            ->where('offer_id', $offer_id)
            ->first();

        return $dataStat;
    }


}
