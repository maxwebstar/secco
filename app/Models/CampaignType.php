<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'campaign_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'ef_key', 'name', 'show', 'position', 'is_lt', 'is_ef'];


    public function getType()
    {
        return $this->where('show', 1)->orderBy('position')->get();
    }

    public function getByNetwork($network)
    {
        switch($network){
            case "lt" :
            case "ef" :
                break;
            default:
                return $this->getType();
                break;
        }

        return $this->where('show', 1)->where('is_' . $network, 1)->orderBy('position')->get();
    }

}
