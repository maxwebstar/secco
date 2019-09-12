<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status',
        'google_id', 'google_token', 'google_refresh_token', 'google_folder',
        'lt_id', 'ef_id', 'pipedrive_id', 'date_approve', 'date_reject',
        'show_for_manage_list', 'show_for_account_manage_list', 'docusign_manager',
        'created_at', 'updaetd_at', 'mongo_user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'google_token'
    ];

    /**
     * Status for user
     *
     * 0 - delete
     * 1 - pending
     * 2 - rejected
     * 3 - approved
     */

    public $arrStatus = [
        0 => "Delete",
        1 => "Pending",
        2 => "Rejected",
        3 => "Approved"
    ];

    public function getStatus()
    {
        return isset($this->arrStatus[$this->status]) ? $this->arrStatus[$this->status] : "None";
    }


    /**
     * Get params for email sendbox.
     *
     * @var Eloquent
     */
    public function param_email()
    {
        return $this->hasOne('App\Models\UserParamEmail', 'user_id', 'id')->withDefault([
            'host' => '',
            'username' => '',
        ]);
    }

    public function getRoleParam($name = "id")
    {
        $role = $this->roles()->first();

        return $role ? $role->{$name} : "";
    }

    public function getRoleFirstPriority($name = "display_name")
    {
        $role = $this->roles()->orderBy('priority', 'desc')->first();

        return $role ? $role->{$name} : "";
    }

    public function getManager()
    {
        return $this->where('show_for_manage_list', 1)->where('status', 3)->get();
    }

    public function getManagerAccount()
    {
        return $this->where('show_for_account_manage_list', 1)->where('status', 3)->get();
    }

    public function getDocusignManager()
    {
        return $this->where('docusign_manager', 1)->where('status', 3)->get();
    }

    public function createGoogleDriveFolder()
    {
        if($this->google_folder || $this->show_for_manage_list == false){
            return false;
        }

        $googleDrive = new \App\Services\GoogleDrive();
        $googleDriveService = $googleDrive->getService();

        $fileMetadata = $googleDrive->getMetadata($this->name, [config('services.google.parent_folder')]);

        $result = $googleDriveService->files->create($fileMetadata);
        if(isset($result->id)){
            $this->google_folder = $result->id;
            return true;
        }

        return false;
    }


    public function setGoogleToken($data)
    {
        $json = is_array($data) ? json_encode($data) : $data;
        $this->google_token = $json;
    }

    public function getGoogleToken()
    {
        return $this->google_token ? json_decode($this->google_token) : [];
    }

}
