<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

//use App\WebLib as weblib;

class Mission extends Model
{

  protected $connection = 'mysql'; //指定db連結 config/database.php
  protected $table = 'missions';




  protected $fillable = [];

  //驗證規則
  public function rules($array)
  {
    $rules = [
      'type' => ['required', 'integer', 'regex:/^[1-9]$/'],
    ];

    $data = [];
    foreach ($array as $item) {
      $data[$item] = $rules[$item];
    }
    return $data;
  }

  //驗證失敗自訂訊息
  public function rulesMessage()
  {
    return [
      'type.required' => '任務類別必填',
      'type.integer' => '任務類別為整數',
      'type.regex' => '任務類別介於範圍1-9',
    ];
  }
}
