<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficPpc extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'traffic_ppc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'value', 'place_holder', 'show', 'position'];


    public function getData()
    {
        return $this->where('show', 1)->orderBy('position')->get();
    }
}
