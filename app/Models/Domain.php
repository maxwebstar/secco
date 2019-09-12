<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'domain';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ef_id', 'value', 'name', 'position', 'show', 'is_lt', 'is_ef'];


    public function getDomain()
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
                return $this->getDomain();
                break;
        }

        return $this->where('show', 1)->where('is_' . $network, 1)->orderBy('position')->get();
    }
}
