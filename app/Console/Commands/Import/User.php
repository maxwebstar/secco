<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Models\User as modelUser;
use App\Models\Entrust\Role as modelRole;

class User extends Command
{
    /**
     * mongodb columns
     *
     * _id
     * active - string [Y/N]
     * banned - string [Y/N] (hard reject)
     * created_at - timestamp
     * gfid - string (google_folder)
     * gplus_id - string (google_id)
     * id - string
     * name - string
     * profilesId - int [1,2] (role, 1 - user, 2 - admin)
     * suspended - string [Y/N] (soft reject)
     */


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-mongo:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from mongo db to mysql';

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
        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('user_profiles')
            /*->where('active', 'Y')
            ->where('banned', 'N')*/
            ->orderBy('created_at')
            ->chunk(100, function ($arrUsers) {

                foreach ($arrUsers as $user) {

                    var_dump($user['email']);

                    $modelUser = new modelUser();
                    $exist = $modelUser->where('mongo_user_id', $user['id'])
                        ->orWhere(function ($query) use ($user){
                            $query->whereNull('mongo_user_id')->where('email', $user['email']);
                        })
                        ->first();

                    if(!$exist) {

                        $password = str_random(10);

                        $newUser = $modelUser->create([
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'password' => Hash::make($password),
                            'status' => $this->getStatus($user),
                            'google_id' => isset($user['gplus_id']) ? $user['gplus_id'] : null,
                            'google_folder' => isset($user['gfid']) ? $user['gfid'] : null,
                            'created_at' => isset($user['created_at']) ? date('Y-m-d H:i:s', $user['created_at']) : date('Y-m-d H:i:s'),
                            'mongo_user_id' => $user['id'],
                        ]);

                        if($user['profilesId'] == 2){
                            $role = modelRole::where('name', 'admin')->first();
                            $newUser->roles()->sync([$role->id]);
                        } else if($user['profilesId'] == 1) {
                            $role = modelRole::where('name', 'sales')->first();
                            $newUser->roles()->sync([$role->id]);
                        }

                    } else if(!$exist['mongo_user_id']) {
                        $exist->mongo_user_id = $user['id'];
                        $exist->save();
                    }
                }
        });
    }


    public function getStatus($user)
    {
        if($user['suspended'] == "Y"){
            return 1;
        } else if($user['active'] == "Y"){
            return 3;
        } else if($user['banned'] == "Y"){
            return 2;
        } else {
            return 0;
        }
    }
}
