<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

//use App\WebLib as weblib;

class Member extends Model
{

  protected $connection = 'mysql';
  protected $table = 'members';


  protected $fillable = [
    'account', 'number', 'register', 'register_at', 'access_token', 'refresh_token', 'access_token_expire_time', 'refresh_token_expire_time', 'is_public',
    'expire_at'
  ];

  public function hasMission()
  {
    return $this->hasOne(Mission::class, 'm_id');
  }

  //驗證規則
  public static function rules($array)
  {
    $rules = [
      'account' => ['required'],
      'unitCode' => ['required', 'digits:6'],
      'id' => ['required', 'regex:/^[A-Z][12]\d{8}$/'],
    ];

    $data = [];
    foreach ($array as $item) {
      $data[$item] = $rules[$item];
    }
    return $data;
  }

  //驗證失敗自訂訊息
  public static function rulesMessage()
  {
    return [
      'account.required' => '序號必填',
      'unitCode.required' => '單位代碼必填',
      'unitCode.digits' => '單位代碼長度錯誤',
      'id.required' => '身分證必填',
      'id.regex' => '身分證格式錯誤'
    ];
  }
}
