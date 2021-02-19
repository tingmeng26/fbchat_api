<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

//use App\WebLib as weblib;

class School extends Model
{

  protected $connection = 'mysql'; //指定db連結 config/database.php
  protected $table = 'school';

  public $fillable = [
    'check_code'
  ];

  public function scopeActive()
  {
    return $this->where('is_public', 1);
  }
}
