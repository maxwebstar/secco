<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'network';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['network_id', 'short_name', 'display_name', 'field_name', 'position', 'by_default', 'show'];


    public function getNetwork()
    {
        return $this->where('show', 1)->orderBy('position')->get();
    }

    public function checkSelected($old = false, $name = 'field_name')
    {
        if($old){
            return $old == $this->{$name} ? true : false;
        } else {
            return $this->by_default ? true : false;
        }
    }
}
