<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pixel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pixel';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'name', 'show', 'position'];


    public function getPixel()
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
                return $this->getPixel();
                break;
        }

        return $this->where('show', 1)->where('is_' . $network, 1)->orderBy('position')->get();
    }

}
