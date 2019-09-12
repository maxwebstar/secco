<?php

use Illuminate\Database\Seeder;

use App\Services\EverFlow\General as EF_General;
use App\Models\Domain as modelDomain;

class DomainEF extends Seeder
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

        modelDomain::where('id', '<=', 10)->where('is_lt', 0)->update(['is_lt' => 1]);

        $efGeneral = new EF_General();

        $efDomain = $efGeneral->getAllDomain();
        if($efDomain){
            foreach($efDomain as $iter){

                $value = str_replace(["www.", "trk.com"], "", $iter->url);
                $data = modelDomain::where('value', $value)->first();
                if($data){

                    $data->ef_id = $iter->network_tracking_domain_id;
                    $data->is_ef = 1;
                    $data->save();

                    $result['update'] ++;

                } else {

                    $data = new modelDomain();
                    $data->fill([
                        'ef_id' => $iter->network_tracking_domain_id,
                        'name' => $iter->url,
                        'show' => 1,
                        'position' => modelDomain::count() + 1,
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
