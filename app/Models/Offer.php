<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'campaign_name', 'campaign_type', 'campaign_link', 'manager_id', 'manager_account_id',
        'advertiser_id', 'advertiser_contact', 'advertiser_email',
        'offer_category_id', 'domain_id', 'pixel_id', 'pixel_location', 'redirect', 'redirect_url',
        'cap_type_id', 'cap_unit_id', 'cap_monetary', 'cap_lead', 'price_in', 'price_out', 'ef_price_in', 'ef_price_out',
        'geos', 'geo_redirect_url',
        'accepted_traffic', 'affiliate_note', 'internal_note', 'need_api_lt', 'need_api_ef', 'lt_id', 'ef_id', 'lt_status', 'ef_status', 'status',
        'created_by', 'created_by_id', 'created_at', 'updated_at',
        'mongo_campaign_id', 'mongo_user_id', 'mongo_id'
    ];

    /**
     * Status for io
     *
     * 1 - New
     * 2 - Declined
     * 3 - Approved
     */

    public $arrStatus = [
        1 => "New",
        2 => "Declined",
        3 => "Approved",
    ];

    public function getStatus()
    {
        return isset($this->arrStatus[$this->status]) ? $this->arrStatus[$this->status] : "None";
    }


    /**
     * Get manager.
     *
     * @var Eloquent
     */
    public function advertiser()
    {
        return $this->hasOne('App\Models\Advertiser', 'id', 'advertiser_id')->withDefault([
            'name' => '',
            'currency_id' => '',
        ]);
    }


    /**
     * Get author.
     *
     * @var Eloquent
     */
    public function created_param()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by_id')->withDefault([
            'name' => '',
        ]);
    }

    /**
     * Get manager.
     *
     * @var Eloquent
     */
    public function manager()
    {
        return $this->hasOne('App\Models\User', 'id', 'manager_id')->withDefault([
            'name' => '',
        ]);
    }

    /**
     * Get manager sale.
     *
     * @var Eloquent
     */
    public function manager_account()
    {
        return $this->hasOne('App\Models\User', 'id', 'manager_account_id')->withDefault([
            'name' => '',
        ]);
    }

    /**
     * Get Campaign Type.
     *
     * @var Eloquent
     */
    public function campaign_type_param()
    {
        return $this->hasOne('App\Models\CampaignType', 'key', 'campaign_type')->withDefault([
            'ef_key' => '',
        ]);
    }

    /**
     * Get Offer Category.
     *
     * @var Eloquent
     */
    public function offer_category()
    {
        return $this->hasOne('App\Models\OfferCategory', 'id', 'offer_category_id')->withDefault([
            'name' => '',
        ]);
    }

    /**
     * Get Pixel.
     *
     * @var Eloquent
     */
    public function pixel()
    {
        return $this->hasOne('App\Models\Pixel', 'id', 'pixel_id')->withDefault([
            'name' => '',
            'key' => '',
        ]);
    }

    /**
     * Get Domain.
     *
     * @var Eloquent
     */
    public function domain()
    {
        return $this->hasOne('App\Models\Domain', 'id', 'domain_id')->withDefault([
            'name' => '',
            'value' => '',
        ]);
    }

    /**
     * Get Cap Type.
     *
     * @var Eloquent
     */
    public function cap_type()
    {
        return $this->hasOne('App\Models\CapType', 'id', 'cap_type_id')->withDefault([
            'name' => '',
        ]);
    }

    /**
     * Get Cap Unit.
     *
     * @var Eloquent
     */
    public function cap_unit()
    {
        return $this->hasOne('App\Models\CapUnit', 'id', 'cap_unit_id');
    }


    /**
     * Get traffic pcc.
     *
     * @var Eloquent
     */
    public function affiliate()
    {
        return $this->belongsToMany('App\Models\Affiliate', 'offer_affiliate', 'offer_id', 'affiliate_id')->withTimestamps();
    }


    /**
     * Get Url.
     *
     * @var Eloquent
     */
    public function offer_url()
    {
        return $this->hasMany('App\Models\OfferUrl', 'offer_id', 'id');
    }


    public function getGeos()
    {
        $result = [];

        if($this->geos){

            $model = new \App\Models\Country();

            $arr = explode(",", $this->geos);
            foreach($arr as $country_key){

                $country = $model->where('key', $country_key)->first();
                if($country){
                    $result[] = $country;
                }
            }
        }

        return $result;
    }


    public function getGeosSelect()
    {
        $result = [];

        $data = $this->getGeos();
        if($data){

            foreach($data as $iter){
                $result[$iter->key] = $iter->name;
            }
        }

        return $result;
    }


    public function updateEFUrl()
    {
        $serviceOffer = new \App\Services\EverFlow\Offer();
        $efOffer = $serviceOffer->getOffer($this->ef_id, "urls");

        if(isset($efOffer->relationship) && isset($efOffer->relationship->urls) && $efOffer->relationship->urls->total){

            $modelOfferURL = new \App\Models\OfferUrl();

            foreach($efOffer->relationship->urls->entries as $url){

                $data = $modelOfferURL->where('offer_id', $this->id)->where('ef_id', $url->network_offer_url_id)->first();
                if($data){
                    $data->url = $url->destination_url;
                    $data->ef_status = $url->url_status;
                } else {
                    $data = new \App\Models\OfferUrl();
                    $data->offer_id = $this->id;
                    $data->name = $url->name;
                    $data->url = $url->destination_url;
                    $data->ef_id = $url->network_offer_url_id;
                    $data->ef_status = $url->url_status;
                }

                $data->save();
            }
        }
    }



}
