<?php

namespace App\Http\Controllers\V1;

use App\Events\ExampleEvent;
use App\Events\SaveLog;
use App\Events\SaveLogEvent;
use App\func;
use \App\Http\Controllers\Controller;
use App\Model\Log;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

class Helper extends Controller
{
  /**
   * 一般文字訊息
   */
  public static function textTemplate($text)
  {
    return [
      'text' => $text
    ];
  }

  /**
   * 一般按鍵訊息   
   */
  public static function buttonTemplate($btn_content, $text = "請選擇")
  {
    $r = [
      'attachment' => [
        "type" => "template",
        "payload" => [
          "template_type" => "button",
          "text" => $text,
          "buttons" => $btn_content
        ]
      ]
    ];

    return  $r;
  }

  /**
   * 處理 post 使用者留言
   */
  public static function processText($text){
    

  }
  /**
   * 處理 post postback互動留言
   */
  public static function processPayload($payload){

  }

  /**
   * 傳入使用者留言 回傳對應題目或文字
   * @param string message
   * @return array message
   */
  public static function getMessage($message)
  {
    switch ($message) {
      case '開始':
        $data = [];
        $text = "Hi! 我是留學國師 aka 雅師。" .
          "或許你正在徬徨無助，或許你只需" .
          "要一點幫助。沒關係！我會盡力協" .
          "助你。接下來，我會問你幾個非常" .
          "簡單的問題，預測最適合你留學的" .
          "國家。準備好我們就開始囉！";
        $button = [
          [
            "type" => "postback",
            "title" => "I’m ready!",
            "payload" => "AGREEMENT"
          ],
        ];
        $data['message'] = self::buttonTemplate($button, $text);;
        $response[] = $data;
        // // 寫入使用者資料
        // $userData = DBResult($data['psid']);
        // // 判斷如果已完成填寫email 不重置結果
        // if (empty($userData['email'])) {
        //   DBReset($data);
        break;
    }
    return $response;
  }

  public static function sendMessage($message)
  {

    $token = env('TOKEN');
    $result = self::postCurl("https://graph.facebook.com/v10.0/me/messages?access_token={$token}", json_encode($message));
    event(new SaveLog('send message: ' . $result));
    return $result;
  }


  public static function postCurl($url, $data, $headers = null, $debug = false, $CA = false, $CApem = "", $timeout = 30)
  {
    //網址,資料,header,返回錯誤訊息,https時驗證CA憑證,CA檔名,超時(秒)
    global $path_cacert;
    $result = "";
    $cacert = $path_cacert . $CApem;
    //CA根证书
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    if ($SSL && $CA && $CApem == "") {
      return "請指定CA檔名";
    }
    if ($headers == null) {
      $headers = [
        'Content-Type: application/json',
      ];
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    //允許執行的最長秒數
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout - 2);
    //連接前等待時間(0為無限)
    //$headers == '' ? '' : curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($SSL && $CA) {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
      // 驗證CA憑證
      curl_setopt($ch, CURLOPT_CAINFO, $cacert);
      // CA憑證檔案位置
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    } elseif ($SSL && !$CA) {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      // 信任任何憑證
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    if ($debug === true && curl_errno($ch)) {
      echo 'GCM error: ' . curl_error($ch);
    }
    curl_close($ch);

    return $result;
  }
}
