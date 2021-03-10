<?php

namespace App\Http\Controllers\V1;

use App\Events\ExampleEvent;
use App\Events\SaveLog;
use App\Events\SaveLogEvent;
use App\func;
use \App\Http\Controllers\Controller;
use App\Model\Log;
use App\Model\Test;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class Fb extends Controller
{
  public function checkFbSetting(Request $request)
  {
    $token = $request->input('hub_verify_token');
    $challenge = $request->input('hub_challenge');
    $verifyToken = env('HUB_VERIFY_TOKEN', null);
    // Log::insert([
    //   'content' => $verifyToken
    // ]);
    if ($token != env('HUB_VERIFY_TOKEN')) {
      // Log::insert([
      //   'content' => "token:$token
      //   /challenge:$challenge/
      //   check fb verify token failed.",

      //   'created_at' => Carbon::now()
      // ]);
      $challenge = '';
    } else {
      // Log::insert([
      //   'content' => "$challenge/success",

      //   'created_at' => Carbon::now()
      // ]);
    }
    return $this->echoMessgae($challenge);
  }

  public function processMessage(Request $request)
  {
    $data = $request->all();
    // event(new SaveLog(json_encode($data)));
    $recipient = $data['entry'][0]['messaging'][0]['sender']['id'] ?? '';
    // 有text物件表示使用者輸入文字
    $text = $data['entry'][0]['messaging'][0]['message']['text'] ?? '';
    // 有postback物件表示使用者透過按鈕互動
    $payload =  $data['entry'][0]['messaging'][0]['postback']['payload'] ?? '';
    $message = [];
    $message['recipient'] = ['id' => $recipient];
    // if (!in_array($text, ['測驗'])) {
    //   $message['message'] =  Helper::textTemplate('你打錯囉！請重新輸入');
    //   $result = Helper::sendMessage($message);
    //   return $this->returndata(['data' => $result]);
    // }
    // 判斷為留言
    // if (!empty($text)) {
    //   $response = Helper::processText($text);
    // } elseif (!empty($payload)) {
    //   $response = Helper::processPayload($payload);
    // }

    $text = empty($text)?$payload:$text;
    // event(new SaveLog($text));
      // event(SaveLog())
    $response = Helper::getMessage($text);
    foreach ($response as $row) {
      $row['recipient'] = ['id' => $recipient];
      Helper::sendMessage($row);
    }
    // $response[0]['recipient'] = ['id' => $recipient];
    // var_dump($response[0]);exit;
    // $message['messaging_type'] = 'RESPONSE';

    // return $this->returndata(['data' => $result]);
  }


  public function test(){
    var_dump(123);exit;
  }
}
