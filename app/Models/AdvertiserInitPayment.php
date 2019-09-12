<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Advertiser as modelAdvertiser;

class AdvertiserInitPayment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advertiser_init_payment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['advertiser_id', 'type', 'updated_at', 'created_at'];


    public function addAdvertiser(modelAdvertiser $data)
    {
        if($data->prepay){
            $dataPayment = new AdvertiserInitPayment();
            $dataPayment->fill([
                'advertiser_id' => $data->id,
                'type' => $data->prepay ? 2 : 1,
            ]);
            $dataPayment->save();
        }
    }


    public function editAdvertiser(modelAdvertiser $data, $old)
    {
        if($data->prepay != $old['prepay']){
            $dataPayment = new AdvertiserInitPayment();
            $dataPayment->fill([
                'advertiser_id' => $data->id,
                'type' => $data->prepay ? 2 : 1,
            ]);
            $dataPayment->save();
        }
    }
}
