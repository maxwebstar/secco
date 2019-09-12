<?php

namespace App\Console\Commands\Import\Request;

use Illuminate\Console\Command;
use DB;
use Validator;
use PDOException;
use Exception;

use App\Models\Request\Cap as modelRequestCap;
use App\Models\User as modelUser;
use App\Models\Offer as modelOffer;
use App\Models\CapType as modelCapType;


class Cap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-mongo:request-cap {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import cap request from mongo db to mysql';

    /**
     *
     * Type command for import advertiser
     *
     * - import
     * - addition
     * - investigate
     */
    protected $type_command;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');

        switch ($type) {
            case "import" :
                $this->import();
                break;
            default :
                throw new Exception('Empty type commaand for import.');
                break;
        }
    }


    protected function import()
    {
        $result = ['create' => 0, 'offer_not_found' => 0];

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('activity')
            ->where('type', "caprequest")
            ->orderBy('time')
            ->chunk(100, function ($arrCap) use ($mongoDB, &$result) {

                foreach($arrCap as $iterCap){

                    $mongo_id = (string) $iterCap['_id'];

                    $exist = modelRequestCap::where('mongo_id', $mongo_id)->first();
                    if(!$exist){

                        $dataOffer = modelOffer::where('lt_id', $iterCap['cid'])->first();
                        if($dataOffer){

                            $created = date('Y-m-d H:i:s', $iterCap['time']);

                            $data = new modelRequestCap();
                            $data->fill([
                                'offer_id' => $dataOffer->id,
                                'date' => date('Y-m-d', $iterCap['edate']),
                                'cap' => $iterCap['newcap'],
                                'cap_reset' => $iterCap['reset'] == "yes" ? 1 : 0,
                                'redirect_url' => $iterCap['redirecturl'],
                                'reason' => $iterCap['reason'] ? : null,
                                'updated_at' => $created,
                                'created_at' => $created,
                                'mongo_id' => $mongo_id,
                            ]);

                            $dataCapType = modelCapType::where('key', $iterCap['captype'])->first();
                            if($dataCapType){
                                $data->cap_type_id = $dataCapType->id;
                            }

                            switch($iterCap['approvalStatus']){
                                case "Approved" :
                                    $data->status = 3;
                                    break;
                                case "Declined" :
                                    $data->status = 2;
                                    break;
                            }

                            $dataUser = modelUser::where('mongo_user_id', $iterCap['creatorId'])->first();
                            if($dataUser){
                                $data->created_by = $dataUser->email;
                                $data->created_by_id = $dataUser->id;
                            } else {
                                $data->created_by = $iterCap['creatorName'];
                                $data->created_by_id = 0;
                            }

                            try {

                                $data->save();

                            } catch (PDOException $e) {
                                var_dump($e->getMessage());
                                dd($iterCap);
                            } catch (Exception $e){
                                var_dump($e->getMessage());
                                dd($iterCap);
                            }

                            $result['create'] ++;

                        } else {

                            $result['offer_not_found'] ++;
                        }
                    }
                }
            });

        var_dump($result);
    }

}