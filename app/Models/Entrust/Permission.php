<?php

namespace App\Models\Entrust;

//use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_id', 'name', 'display_name', 'description', 'position', 'show'];


    /**
     * Get permission group.
     *
     * @var Eloquent
     */
    public function permissin_group()
    {
        return $this->hasOne('App\Models\Entrust\PermissionGroup', 'id', 'group_id');
    }
}
