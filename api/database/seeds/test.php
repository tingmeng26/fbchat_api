<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class test extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('flights')->insert([
        'name' => Str::random(10),
        'airline' =>Str::random(10),
        'created_at'=>Carbon::now(),
        'updated_at'=>Carbon::now()
    ]);
    }
}
