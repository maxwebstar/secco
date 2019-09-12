<?php

namespace App\Console\Commands\Import\Request;

use Illuminate\Console\Command;
use DB;
use Validator;
use PDOException;
use Exception;

use App\Models\Request\Status as modelRequestStatus;
use App\Models\User as modelUser;
use App\Models\Offer as modelOffer;


class Status extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-mongo:request-status {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import status request from mongo db to mysql';

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
        $result = ['create' => 0, 'offer_id_empty' => 0, 'offer_not_found' => 0];

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('activity')
            ->where('type', "statusChange")
            ->orderBy('time')
            ->chunk(100, function ($arrCap) use ($mongoDB, &$result) {

                foreach($arrCap as $iterStatus){

                    if(empty($iterStatus['cid'])){
                        $result['offer_id_empty'] ++;
                        continue;
                    }

                    $mongo_id = (string) $iterStatus['_id'];

                    $exist = modelRequestStatus::where('mongo_id', $mongo_id)->first();
                    if(!$exist){

                        $dataOffer = modelOffer::where('lt_id', $iterStatus['cid'])->first();
                        if($dataOffer){

                            $created = date('Y-m-d H:i:s', $iterStatus['time']);

                            $data = new modelRequestStatus();
                            $data->fill([
                                'offer_id' => $dataOffer->id,
                                'date' => date('Y-m-d', $iterStatus['edate']),
                                'need_api_lt' => 1,
                                'lt_status' => $iterStatus['newstatus'],
                                'cap_reset' => $iterStatus['massnotice'] == "yes" ? 1 : 0,
                                'redirect_url' => $iterStatus['redirecturl'],
                                'reason' => $iterStatus['reason'] ? : null,
                                'updated_at' => $created,
                                'created_at' => $created,
                                'mongo_id' => $mongo_id,
                            ]);

                            switch($iterStatus['approvalStatus']){
                                case "Approved" :
                                    $data->status = 3;
                                    break;
                                case "Declined" :
                                    $data->status = 2;
                                    break;
                            }

                            $dataUser = modelUser::where('mongo_user_id', $iterStatus['creatorId'])->first();
                            if($dataUser){
                                $data->created_by = $dataUser->email;
                                $data->created_by_id = $dataUser->id;
                            } else {
                                $data->created_by = $iterStatus['creatorName'];
                                $data->created_by_id = 0;
                            }

                            try {

                                $data->save();

                            } catch (PDOException $e) {
                                var_dump($e->getMessage());
                                dd($iterStatus);
                            } catch (Exception $e){
                                var_dump($e->getMessage());
                                dd($iterStatus);
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