<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'access';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'label', 'value', 'position', 'show'];

}
