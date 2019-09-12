<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Massadjustment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'massadjustment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ef_adjustment_id', 'affiliate_id', 'offer_id', 'date', 'total_click', 'unique_click', 'conversion', 'payout', 'revenue', 'type', 'note', 'relationship', 'created_by', 'created_by_id'];


    public function exist($affiliate_id, $offer_id, $date, $type)
    {
        $data = $this->where('affiliate_id', $affiliate_id)->where('offer_id', $offer_id)->where('date', $date)->where('type', $type)->first();

        return $data;
    }


    public $arrType = [
        1 => 'Import from LT',
    ];


    public function getType()
    {
        return isset($this->arrType[$this->type]) ? $this->arrType[$this->type] : null;
    }


    public function compareWith($data, $type)
    {
        if($this->affiliate_id != $data['network_affiliate_id'] ||
            $this->offer_id != $data['network_offer_id'] ||
            $this->total_click != $data['total_clicks'] ||
            $this->unique_click != $data['unique_clicks'] ||
            $this->conversion != $data['conversions'] ||
            $this->payout != $data['payout'] ||
            $this->revenue != $data['revenue'] ||
            $this->date != $data['date_adjustment'] ||
            $this->type != $type){
            return true;
        } else {
            return false;
        }
    }

}
