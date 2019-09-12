<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplateGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_template_group';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'display_name', 'show', 'position'];

    /**
     * Get templates.
     *
     * @var Eloquent
     */
    public function templates()
    {
        return $this->hasMany('App\Models\EmailTemplate', 'group_id', 'id');
    }


}
