<?php

namespace App\Models\QB;

use Illuminate\Database\Eloquent\Model;
use DB;

class Report extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'qb_advertiser_report';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['advertiser_id', 'quickbook_id', 'qb_number', 'currency_id', 'amount', 'type', 'date', 'updated_qb', 'created_qb', 'updated_at', 'created_at'];

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


    public function getARByAdvertiser($advertiser_id)
    {
        $sqlDB = DB::connection('mysql');

        $dataInvoice = $sqlDB->table($this->table)
            ->select(DB::raw('SUM(amount) as amount'))
            ->where('advertiser_id', $advertiser_id)
            ->where('type', 1)
            ->first();

        $dataPayment = $sqlDB->table($this->table)
            ->select(DB::raw('SUM(amount) as amount'))
            ->where('advertiser_id', $advertiser_id)
            ->where('type', 2)
            ->first();

        if($dataInvoice){
            if($dataPayment){

                $result = $dataInvoice->amount - $dataPayment->amount;

                return $result < 0 ? 0 : $result;

            } else {
                return $dataInvoice->amount;
            }
        } else {
            return 0;
        }
    }


    public function getInvoiceByAdvertiser($advertiser_id)
    {
        $sqlDB = DB::connection('mysql');

        $dataInvoice = $sqlDB->table($this->table)
            ->select(DB::raw('SUM(amount) as amount'))
            ->where('advertiser_id', $advertiser_id)
            ->where('type', 1)
            ->first();

        if($dataInvoice){

            return $dataInvoice->amount;

        } else {
            return 0;
        }
    }


    public function getPaymentByAdvertiser($advertiser_id)
    {
        $sqlDB = DB::connection('mysql');

        $dataPayment = $sqlDB->table($this->table)
            ->select(DB::raw('SUM(amount) as amount'))
            ->where('advertiser_id', $advertiser_id)
            ->where('type', 2)
            ->first();

        if($dataPayment){

            return $dataPayment->amount;

        } else {
            return 0;
        }
    }

}
