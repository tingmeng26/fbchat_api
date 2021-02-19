<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

//use App\WebLib as weblib;

class Group extends Model {

    protected $connection = 'mysql'; //指定db連結 config/database.php
    protected $table = 'groups';

   public $fillable = [
     'expire_time','updated_at'
   ];

}
