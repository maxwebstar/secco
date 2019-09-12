<?php

use Illuminate\Database\Seeder;

use App\Services\EverFlow\General as EF_General;
use App\Models\OfferCategory as modelCategory;

class OfferCategoryEF extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = [
            'update' => 0,
            'create' => 0
        ];

        modelCategory::where('id', '<=', 25)->where('is_lt', 0)->update(['is_lt' => 1]);

        $efGeneral = new EF_General();

        $efCategory = $efGeneral->getAllCategory();
        if($efCategory){
            foreach($efCategory as $iter){

                $data = modelCategory::where('name', $iter->name)->first();
                if($data){

                    $data->ef_id = $iter->network_category_id;
                    $data->is_ef = 1;
                    $data->save();

                    $result['update'] ++;

                } else {

                    $data = new modelCategory();
                    $data->fill([
                        'ef_id' => $iter->network_category_id,
                        'name' => $iter->name,
                        'show' => 1,
                        'position' => modelCategory::count() + 1,
                        'is_ef' => 1,
                    ]);
                    $data->save();

                    $result['create'] ++;
                }
            }
        }

        dd($result);
    }
}
