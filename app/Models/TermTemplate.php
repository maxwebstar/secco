<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermTemplate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'term_template';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['display_name', 'text', 'description', 'position', 'by_default', 'show'];


    public function getTemplate()
    {
        return $this->where('show', 1)->orderBy('position')->get();
    }

}
