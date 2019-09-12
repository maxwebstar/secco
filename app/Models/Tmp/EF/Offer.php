<?php

namespace App\Models\Tmp\EF;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $primaryKey = 'network_offer_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tmp_ef_offer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['network_offer_id', 'network_id', 'network_advertiser_id', 'network_offer_group_id',	'name',	'thumbnail_url', 'network_category_id',	'internal_notes', 'destination_url', 'server_side_url',	'is_view_through_enabled', 'view_through_destination_url', 'preview_url', 'offer_status', 'currency_id', 'project_id', 'relationship'];


    public function saveData($ef)
    {
        $exist = $this->where('network_offer_id', $ef->network_offer_id)->first();

        if ($exist) {
            $data = $exist;
        } else {
            $data = new $this;
            $data->network_offer_id = $ef->network_offer_id;
        }

        $data->fill([
            'network_id' => $ef->network_id,
            'network_advertiser_id' => $ef->network_advertiser_id,
            'network_offer_group_id' => $ef->network_offer_group_id,
            'name' => $ef->name,
            'thumbnail_url' => $ef->thumbnail_url,
            'network_category_id' => $ef->network_category_id,
            'internal_notes' => $ef->internal_notes,
            'destination_url' => $ef->destination_url,
            'server_side_url' => $ef->server_side_url,
            'is_view_through_enabled' => $ef->is_view_through_enabled,
            'view_through_destination_url' => $ef->view_through_destination_url,
            'preview_url' => $ef->preview_url,
            'offer_status' => $ef->offer_status,
            'currency_id' => $ef->currency_id,
            'project_id' => $ef->project_id,
            'relationship' => $ef->relationship ? json_encode($ef->relationship) : null,
        ]);

        $data->save();
    }

}
