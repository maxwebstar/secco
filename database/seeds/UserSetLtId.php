<?php

use Illuminate\Database\Seeder;
use App\Models\User as modelUser;

class UserSetLtId extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mangers = array(
            '10021' => 'Jon Lender',
            '13695' => 'Andrew Gold',
            '14257' => 'Marlon Smith',
            '14327' => 'Khaki Martin',
            '11816' => 'Derrick Lachmann',
            '14553' => 'Caroline Grymes'
        );

        foreach($mangers as $lt_id => $user_name){

            $data = modelUser::where('name', $user_name)->where('lt_id', 0)->first();
            if($data){
                $data->lt_id = $lt_id;
                $data->save();
            }
        }
    }
}
