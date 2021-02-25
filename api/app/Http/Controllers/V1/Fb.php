<?php

namespace App\Http\Controllers\V1;

use \App\Http\Controllers\Controller;
use App\Model\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class Fb extends Controller
{
  public function checkFbSetting(Request $request)
  {
    $token = $request->input('hub_verify_token');
    $challenge = $request->input('hub_challenge');
    $verifyToken = env('HUB_VERIFY_TOKEN',null);
    Log::insert([
      'content'=>$verifyToken
    ]);
    if($token != env('HUB_VERIFY_TOKEN')){
      Log::insert([
        'content'=>"token:$token
        /challenge:$challenge/
        check fb verify token failed.",

        'created_at'=>Carbon::now()
      ]);
      $challenge = '';
    }else{
      Log::insert([
        'content'=>"$challenge/success",

        'created_at'=>Carbon::now()
      ]);
    }
    return $this->echoMessgae($challenge);


  }
}
