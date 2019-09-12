<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'currency';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'key', 'rate', 'sign', 'position', 'show'];


    public function getCurrency()
    {
        return $this->where('show', 1)->orderBy('position')->get();
    }


    public function getAllCurrencyKeyID($field = "name")
    {
        $data = $this->orderBy('position')->get();

        $result = [];
        foreach($data as $iter){
            $result[$iter->id] = $iter->{$field};
        }

        return $result;
    }
}
