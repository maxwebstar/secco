<?php

namespace App\Models\Entrust;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions_group';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'display_name', 'description', 'position', 'show'];

    public function permissins()
    {
        return $this->hasMany('App\Models\Entrust\Permission', 'group_id', 'id');
    }


}
