<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IOTemplateDoc extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'io_template_doc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'file_name', 'position', 'by_default', 'show'];


    public $path_docx = "io/docx/template/";


    public function getTemplate()
    {
        return $this->where('show', 1)->orderBy('position')->get();
    }
}
