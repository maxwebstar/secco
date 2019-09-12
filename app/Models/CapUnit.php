<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapUnit extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cap_unit';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'name', 'show', 'position'];


    public function getUnit()
    {
        return $this->where('show', 1)->orderBy('position')->get();
    }

}
