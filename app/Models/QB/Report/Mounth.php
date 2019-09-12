<?php

namespace App\Models\QB\Report;

use Illuminate\Database\Eloquent\Model;

class Mounth extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'qb_advertiser_report_mounth';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['advertiser_id',	'amount', 'type', 'date', 'month', 'year'];

    /**
     * Type for report
     *
     * 1 - invoice
     * 2 - payment
     */

    public $arrType = [
        1 => "Invoice",
        2 => "Payment",
    ];

    public function getType()
    {
        return isset($this->arrType[$this->type]) ? $this->arrType[$this->type] : "None";
    }


    public function getInvoiceByAdvertiser($id)
    {
        $result['count'] = 0;
        $result['sum'] = 0;
        $result['avg'] = 0;

        $data = $this->where('advertiser_id', $id)->where('type', 1)->orderBy('date', 'DESC')->limit(6)->get();

        if($data->count()){

            $result['count'] = $data->count();

            foreach($data as $iter){
                $result['sum'] += $iter->amount;
            }

            $result['avg'] = round($result['sum'] / $result['count'], 2);
        }

        return $result;
    }
}
