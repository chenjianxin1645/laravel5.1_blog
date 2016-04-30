<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

//         $this->call(PostsTableSeeder::class);
        /*
         * 填充tags和posts
         * */
         $this->call('TagTableSeeder');
         $this->call('PostsTableSeeder');


        Model::reguard();
    }
}


