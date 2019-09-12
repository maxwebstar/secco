<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IOtrafficPpc extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'io_traffic_ppc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['io_id', 'traffic_id'];
}
