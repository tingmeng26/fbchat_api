<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

//use App\WebLib as weblib;

class LotteryPool extends Model
{

  protected $connection = 'mysql'; //指定db連結 config/database.php
  protected $table = 'lottery_pool';

  protected $fillable = [
    'm_id'
  ];
}
