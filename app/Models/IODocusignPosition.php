<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IODocusignPosition extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'io_docusign_position';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name',
        'secco_string', 'secco_units', 'secco_x_offset', 'secco_y_offset',
        'client_string', 'client_units', 'client_x_offset',	'client_y_offset',
        'type',	'template_id', 'position'
    ];

    public function getByTemplate($id)
    {
        $data = $this->where('template_id', $id)->orderBy('position')->get();
        if($data){

            $result = [];
            foreach($data as $iter){
                $result[$iter->type] = $iter;
            }

            return $result;
        } else {
            return false;
        }
    }
}
