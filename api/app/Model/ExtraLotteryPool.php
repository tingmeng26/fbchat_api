<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

//use App\WebLib as weblib;

class ExtraLotteryPool extends Model
{

  protected $connection = 'mysql'; //指定db連結 config/database.php
  protected $table = 'extra_lottery_pool';

  protected $fillable = [
    'm_id'
  ];
}
