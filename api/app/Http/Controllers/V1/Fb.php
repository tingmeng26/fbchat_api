<?php

namespace App\Http\Controllers\V1;

use App\Events\ExampleEvent;
use App\Events\SaveLog;
use App\Events\SaveLogEvent;
use \App\Http\Controllers\Controller;
use App\Model\Log;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

class Fb extends Controller
{
  public function checkFbSetting(Request $request)
  {
    $token = $request->input('hub_verify_token');
    $challenge = $request->input('hub_challenge');
    $verifyToken = env('HUB_VERIFY_TOKEN', null);
    Log::insert([
      'content' => $verifyToken
    ]);
    if ($token != env('HUB_VERIFY_TOKEN')) {
      Log::insert([
        'content' => "token:$token
        /challenge:$challenge/
        check fb verify token failed.",

        'created_at' => Carbon::now()
      ]);
      $challenge = '';
    } else {
      Log::insert([
        'content' => "$challenge/success",

        'created_at' => Carbon::now()
      ]);
    }
    return $this->echoMessgae($challenge);
  }

  public function processMessage(Request $request)
  {

    $data = $request->all();
    // event(new SaveLog('test event'));
    // event(new SaveLog('fukkk'));
    // event(new ExampleEvent('fu'));
    event(new SaveLog('error message'));
    // Log::create(['content'=>'fuck!']);
    
    // Log::create([
    //   'content'=>json_encode($data)
    // ]);
    $recipient = $data['entry'][0]['messaging'][0]['recipient']['id']??'';
    $text = $data['entry'][0]['messaging'][0]['message']['text']??'';
    // Log::create([
    //   'content'=>$recipient.$text
    // ]);
  }
}
