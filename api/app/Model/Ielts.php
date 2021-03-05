<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

//use App\WebLib as weblib;

class Ielts extends Model
{

  protected $connection = 'mysql'; //指定db連結 config/database.php
  protected $table = 'ielts';

  public $fillable = [
    'fbid', 'name', 'a1', 'a2', 'a3', 'a4', 'a5', 'result', 'email', 'agree'
  ];
}
