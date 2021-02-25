<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Predis\Connection\ConnectionException;

//use Illuminate\Http\Request;

/**
 *
 * @package App\Http\Controllers
 *
 * @SWG\Swagger(
 *     basePath="api/public/api/",
 *     host="localhost/fbchat_api/",
 *     schemes={"http","https"},
 *     consumes = {"application/x-www-form-urlencoded","application/json","multipart/form-data"},
 *     produces = {"application/json"},
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="貝多芬250年線上活動 API文件",
 *         description="API規格書,登入狀態Header帶Authorization 會員 access token",
 *         termsOfService="coder"
 *     ),
 *     @SWG\SecurityScheme(
 *         securityDefinition="MyHeaderAuthentication-Authorization",
 *         type="apiKey",
 *         in="header",
 *         name="Authorization"
 *     ),
 * )
 */

class Controller extends BaseController
{
  protected $isRedisConnect = false;
  private $sqls = [];
  function __construct()
  {
    if (env('API_DEBUG') == true) {
      DB::listen(function ($query) {
        $bindings = $query->bindings;
        $sql = $query->sql;
        foreach ($bindings as $replace) {
          $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
          $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        $this->sqls[] = $sql . '________(time:' . $query->time . ')';
      });
    }

    try {
      Redis::ping();
      $this->isRedisConnect = true;
    } catch (ConnectionException $e) {
      $this->isRedisConnect = false;
    }
  }
  public function ruleValidator($request, $rule = [], $message = [], $otherary = [])
  {
    $requestdata = [];
    if (is_array($request)) {
      $requestdata = $request;
    } else {
      $requestdata = $request->all();
    }
    $requestdata = array_merge($requestdata, $otherary);

    $validator = Validator::make($requestdata, $rule, $message);
    if ($validator->fails()) {
      foreach ($validator->errors()->getMessages() as $message) {
        return isset($message[0]) ? $message[0] : null;
      }
    }
    return null;
  }
  public function error($data = [], $status_code = 400)
  {
    $data['success'] = false;
    return $this->returndata($data, $status_code);
  }
  public function success($data = [])
  {
    $data['success'] = true;
    $data['msgcode'] = isset($data['msgcode']) ? (int)$data['msgcode'] : 000000;
    return $this->returndata($data, 200);
  }
  //回傳json訊息(預設成功 0000)
  public function returndata($data = [], $status_code = 200)
  {
    $success = isset($data['success']) ? $data['success'] : true;
    $msgcode = isset($data['msgcode']) ? (int)$data['msgcode'] : 0;

    $msg = self::getMsgString($msgcode);
    $msg .= (isset($data['msg']) && $data['msg'] != "") ? ($msg != '' ? '-' : '') . $data['msg'] : '';
    $data = isset($data['data']) ? $data['data'] : '';
    $resp = [
      'success' => $success,
      'data' => $data,
      'msg' => $msg,
      'msgcode' => str_pad($msgcode, 6, '0', STR_PAD_LEFT) //6位數代碼
    ];
    if (env('API_DEBUG') == true) {
      $resp['sql'] = $this->sqls;
    }
    return response()->json($resp, $status_code);
  }

  public static function getMsgString($msgcode)
  {
    $errcode = config('errcode.code');
    if (isset($errcode[$msgcode])) {
      return $errcode[$msgcode];
    }
    return '';
  }

  public static function echoMessgae($message){
    echo $message;
  }
}
