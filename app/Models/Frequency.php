<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frequency extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'frequency';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'lt_name', 'ef_name', 'position', 'show'];


    public function getFrequency()
    {
        return $this->where('show', 1)->orderBy('position')->get();
    }
}
