<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offer_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ef_id', 'name', 'show', 'position', 'is_lt', 'is_ef'];


    public function getCategory()
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
                return $this->getCategory();
                break;
        }

        return $this->where('show', 1)->where('is_' . $network, 1)->orderBy('position')->get();
    }
}
