<?php

namespace App\Models\Tmp\EF;

use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    protected $primaryKey = 'network_affiliate_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tmp_ef_affiliate';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['network_affiliate_id', 'network_id', 'name', 'network_employee_id',	'has_notifications', 'network_traffic_source_id', 'account_executive_id', 'adress_id', 'default_currency_id', 'is_contact_address_enabled',	'time_created',	'time_saved', 'relationship'];


    public function saveData($ef)
    {
        $exist = $this->where('network_affiliate_id', $ef->network_affiliate_id)->first();

        if($exist){
            $data = $exist;
        } else {
            $data = new $this;
            $data->network_affiliate_id = $ef->network_affiliate_id;
        }

        $data->fill([
            'network_id' => $ef->network_id,
            'name' => $ef->name,
            'network_employee_id' => $ef->network_employee_id,
            'has_notifications' => $ef->has_notifications,
            'network_traffic_source_id' => $ef->network_traffic_source_id,
            'account_executive_id' => $ef->account_executive_id,
            'adress_id' => $ef->adress_id,
            'default_currency_id' => $ef->default_currency_id,
            'is_contact_address_enabled' => $ef->is_contact_address_enabled,
            'time_created' => $ef->time_created ? date("Y-m-d H:i:s", $ef->time_created) : null,
            'time_saved' => $ef->time_saved ? date("Y-m-d H:i:s", $ef->time_saved) : null,
            'relationship' => $ef->relationship ? json_encode($ef->relationship) : null,
        ]);

        $data->save();
    }

}
