<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cap_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'name', 'show', 'position'];


    public function getType()
    {
        return $this->where('show', 1)->orderBy('position')->get();
    }
}
