<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertiser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advertiser';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'contact', 'email',
        'country', 'state', 'province', 'city', 'street1', 'street2', 'zip', 'phone',
        'currency_id', 'prepay', 'prepay_amount', 'cap', 'frequency_id', 'frequency_custom',
        'google_folder', 'manager_id', 'manager_account_id', 'quickbook_id', 'lt_id', 'ef_id', 'ef_status', 'pipedrive_id',
        'edited_by', 'edited_at', 'created_by', 'created_by_id', 'created_at', 'updated_at', 'mongo_id',
    ];

    /**
     * Get manager.
     *
     * @var Eloquent
     */
    public function manager()
    {
        return $this->hasOne('App\Models\User', 'id', 'manager_id')->withDefault([
            'name' => '',
            'email' => '',
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
            'email' => '',
        ]);
    }

    /**
     * Get country.
     *
     * @var Eloquent
     */
    public function country_param()
    {
        return $this->hasOne('App\Models\Country', 'key', 'country');
    }

    /**
     * Get state.
     *
     * @var Eloquent
     */
    public function state_param()
    {
        if($this->state) {
            return $this->hasOne('App\Models\State', 'key', 'state');
        } else {
            return false;
        }
    }

    /**
     * Get statistic.
     *
     * @var Eloquent
     */
    public function statistic()
    {
        return $this->hasMany('App\Models\AdvertiserStat', 'advertiser_id', 'id');
    }

    /**
     * Get currency.
     *
     * @var Eloquent
     */
    public function currency()
    {
        return $this->hasOne('App\Models\Currency', 'id', 'currency_id');
    }


    public function checkGoogleAccess()
    {
        $googleDrive = new \App\Services\GoogleDrive();
        $googleDrive->getService();
    }


    public function createGoogleDriveFolder()
    {
        $managerFolder = $this->manager->google_folder;

        if($this->google_folder || $managerFolder == false){
            return false;
        }

        $googleDrive = new \App\Services\GoogleDrive();
        $googleDriveService = $googleDrive->getService();

        $fileMetadata = $googleDrive->getMetadata($this->name, [$managerFolder]);

        $result = $googleDriveService->files->create($fileMetadata);
        if(isset($result->id)){
            $this->google_folder = $result->id;
            return true;
        }

        return false;
    }

}
