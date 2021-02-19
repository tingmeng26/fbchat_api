<?php

namespace App\Http\Controllers\V1;

use \App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Model as WebModel;
use App\Model\Group;
use App\Model\Member as ModelMember;
use App\Model\Mission;
use App\Model\School;
use App\Model\Survey;
use App\WebLib;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Predis\Connection\ConnectionException;

class Member extends Controller
{

  #region [Login Swagger note]
  /**
   * @SWG\Get(
   *     path="/city",
   *     tags={"APP - 會員"},
   *     description="取得縣市(通路)名稱及縣市代碼選單，用以取得該縣市學校(單位)，",
   *     summary = "縣市",
   *     @SWG\Response(
   *         response = "200",
   *         description = "SUCCESS,正常登入情況",
   *         @SWG\Schema(
   *               @SWG\Property(
   *                   property="success",
   *                   type="boolean",
   *                   description="回傳結果(true|false)"
   *               ),
   * *                    @SWG\Property(property="data", type="array",
   *                  @SWG\Items(
   * *                      @SWG\Property(property="code", type="string",description="縣市代碼"),
   * *                      @SWG\Property(property="name", type="string",description="縣市名稱"),
   *                    ),
   * ),
   *               @SWG\Property(
   *                   property="msg",
   *                   type="string",
   *                   description="狀態代碼的文字說明"
   *               ),
   *               @SWG\Property(
   *                   property="msgcode",
   *                   type="string",
   *                   description="狀態代碼"
   *               ),
   *         )
   *     ),
   *     @SWG\Response(
   *         response="401",
   *         description="HTTP 401 Error (身分驗證授權失敗)"
   *     ),
   *     @SWG\Response(
   *         response="403",
   *         description="HTTP 403 Error (操作失敗)"
   *     ),
   *     @SWG\Response(
   *         response="404",
   *         description="HTTP 404 Error (查無指定頁面)"
   *     ),
   *     @SWG\Response(
   *         response="405",
   *         description="HTTP 405 Error (HTTP Request Method錯誤)"
   *     ),
   *     @SWG\Response(
   *         response="500",
   *         description="HTTP 500 Error (伺服器錯誤)"
   *     )
   * )
   */

  public function getCity()
  {
    $data = config('app.CITY') ?? [];
    return $this->returndata(['data' => $data]);
  }

  #region [Login Swagger note]
  /**
   * @SWG\Get(
   *     path="/school/{code}",
   *     tags={"APP - 會員"},
   *     description="輸入縣市代碼取得所有學校(單位)，並回傳各學校檢檢碼，請前端於呼叫/login api前先行確認該學校檢核碼與登入之序號前3碼是否相同",
   *     summary = "學校",
   *  @SWG\Parameter(
   *         name="code",
   *         in="path",
   *         description="縣市代碼",
   *         type="string",
   *         required=true,
   *         default="",
   *     ),
   *     @SWG\Response(
   *         response = "200",
   *         description = "SUCCESS,正常登入情況",
   *         @SWG\Schema(
   *               @SWG\Property(
   *                   property="success",
   *                   type="boolean",
   *                   description="回傳結果(true|false)"
   *               ),
   * *                    @SWG\Property(property="data", type="array",
   *                  @SWG\Items(
   * *                      @SWG\Property(property="code", type="string",description="學校(單位)代碼"),
   * *                      @SWG\Property(property="checkCode", type="string",description="檢核碼"),
   * *                      @SWG\Property(property="name", type="string",description="學校(單位)名稱"),
   *                    ),
   * ),
   *               @SWG\Property(
   *                   property="msg",
   *                   type="string",
   *                   description="狀態代碼的文字說明"
   *               ),
   *               @SWG\Property(
   *                   property="msgcode",
   *                   type="string",
   *                   description="狀態代碼"
   *               ),
   *         )
   *     ),
   *     @SWG\Response(
   *         response="401",
   *         description="HTTP 401 Error (身分驗證授權失敗)"
   *     ),
   *     @SWG\Response(
   *         response="403",
   *         description="HTTP 403 Error (操作失敗)"
   *     ),
   *     @SWG\Response(
   *         response="404",
   *         description="HTTP 404 Error (查無指定頁面)"
   *     ),
   *     @SWG\Response(
   *         response="405",
   *         description="HTTP 405 Error (HTTP Request Method錯誤)"
   *     ),
   *     @SWG\Response(
   *         response="500",
   *         description="HTTP 500 Error (伺服器錯誤)"
   *     )
   * )
   */
  #region [Login Swagger note]

  public function getSchool($code = '')
  {
    try {
      $data = [];
      if (empty($code)) {
        throw new CustomException("縣市代碼必填", 400001, 400);
      }
      if ($code == config('app.REDIS_IS_NOT_CONNECTED')) {
        throw new CustomException('請檢查redis相關設定', 401001, 401);
      }
      $cityCodeArray = [];
      // 檢查 redis 是否正常
      if ($this->isRedisConnect) {
        $cityCodeArray = Redis::get('cityCode');
      }
      // 檢查縣市代碼是否存在
      if (empty($cityCodeArray)) {
        $sql = "select city_code from school group by city_code";
        $cityData = DB::select($sql);
        $cityCodeArray = [];
        foreach ($cityData as $row) {
          array_push($cityCodeArray, $row->city_code);
        }
        if ($this->isRedisConnect) {
          Redis::set('cityCode', json_encode($cityCodeArray));
        }
      } else {
        $cityCodeArray = json_decode(Redis::get('cityCode'), 1);
      }
      if (!in_array($code, $cityCodeArray, true)) {
        throw new CustomException('縣市代碼錯誤', 403001, 403);
      }
      $schoolData = [];
      if ($this->isRedisConnect) {
        $schoolData = Redis::hget('school', $code);
      }
      // 取得該縣市所有學校
      if (empty($schoolData)) {
        $schoolData = School::active()->where('city_code', $code)->get()->toArray();
        if (empty($schoolData)) {
          throw new CustomException('縣市代碼錯誤', 403001, 403);
        }
        foreach ($schoolData as $row) {
          array_push($data, [
            'code' => $row['school_code'],
            'checkCode' => $row['check_code'],
            'name' => $row['school_name']
          ]);
        }
        if ($this->isRedisConnect) {
          Redis::hset('school', $code, json_encode($data));
        }
      } else {
        $schoolData = Redis::hget('school', $code);
        $data = json_decode($schoolData, 1);
      }

      return $this->returndata(['data' => $data]);
    } catch (ConnectionException $e) {
      return $this->error(['msgcode' => 401001, 'msg' => '請檢查redis相關設定'], 401);
    } catch (CustomException $ex) {
      return $this->error(['msgcode' => $ex->getCustomCode(), 'msg' => $ex->getCustomMessage()], $ex->getStatusCode());
    } catch (Exception $ex) {
      return $this->error(['msg' => '執行階段錯誤：' . $ex->getMessage()], 500);
    }
  }
  /**
   * @SWG\Post(
   *     path="/login",
   *     tags={"APP - 會員"},
   *     description="使用單位代碼、序號、身分證進行登入，登入成功回傳會員任務完成情況、access token、access token expire time
   *      測試序號 test1~5 單位代碼 1111111 身分證 A123456789 
   *      狀態代碼(000000為正常，400001為欄位不齊全，403001為資料驗證失敗 )",
   *     summary = "登入",
   *     
   *     @SWG\Parameter(
   *          name="unitCode",
   *         in="formData",
   *         description="單位代碼",
   *         type="string",
   *         required=true,
   *         default="",
   *     ),
   *  @SWG\Parameter(
   *         name="account",
   *         in="formData",
   *         description="序號",
   *         type="string",
   *         required=true,
   *         default="",
   *     ),
   *  @SWG\Parameter(
   *         name="id",
   *         in="formData",
   *         description="身份證",
   *         type="string",
   *         required=true,
   *         default="",
   *     ),
   *     @SWG\Response(
   *         response = "200",
   *         description = "SUCCESS,正常登入情況",
   *         @SWG\Schema(
   *               @SWG\Property(
   *                   property="success",
   *                   type="boolean",
   *                   description="回傳結果(true|false)"
   *               ),
   *               @SWG\Property(
   *                   property="data",
   *                   type="object",
   *                   @SWG\Property(property="accessToken", type="string", description="accessToken"),
   *                   @SWG\Property(property="accessTokenExpireTime", type="integer", description="access token 到期時間timestamp"),
   *                        ),
   *                      ),
   *               ),
   *               @SWG\Property(
   *                   property="msg",
   *                   type="string",
   *                   description="狀態代碼的文字說明"
   *               ),
   *               @SWG\Property(
   *                   property="msgcode",
   *                   type="string",
   *                   description="狀態代碼"
   *               ),
   *         )
   *     ),
   *     @SWG\Response(
   *         response="401",
   *         description="HTTP 401 Error (身分驗證授權失敗)"
   *     ),
   *     @SWG\Response(
   *         response="403",
   *         description="HTTP 403 Error (操作失敗)"
   *     ),
   *     @SWG\Response(
   *         response="404",
   *         description="HTTP 404 Error (查無指定頁面)"
   *     ),
   *     @SWG\Response(
   *         response="405",
   *         description="HTTP 405 Error (HTTP Request Method錯誤)"
   *     ),
   *     @SWG\Response(
   *         response="500",
   *         description="HTTP 500 Error (伺服器錯誤)"
   *     )
   * )
   */
  #endregion

  public function login(REQUEST $request)
  {
    try {
      $data = $request->all();
      $account = empty($data['account']) ? '' : trim($data['account']);
      $unitCode = empty($data['unitCode']) ? '' : trim($data['unitCode']);
      $id = empty($data['id']) ? '' : trim($data['id']);
      if (empty($account) || empty($unitCode) || empty($id)) {
        throw new CustomException('序號、單位代碼、身份證為必填', 400001, 400);
      }
      $account = strtolower($account);
      // 驗證資料
      $response = $this->ruleValidator([
        'account' => $account,
        'unitCode' => $unitCode,
        'id' => $id
      ], ModelMember::rules(['account', 'unitCode', 'id']), ModelMember::rulesMessage());
      if ($response) {
        throw new CustomException($response, 400001, 400);
      }

      // 透過單位代碼取得檢核碼
      $checkCode = '';
      if ($this->isRedisConnect) {
        $checkCode =  Redis::hget('checkCode', $unitCode);
      }
      if (empty($checkCode)) {
        $schoolData = School::active()->select('check_code')->where('school_code', $unitCode)->first();
        if (empty($schoolData)) {
          throw new CustomException('單位代碼錯誤', 403001, 403);
        }
        $checkCode = $schoolData->check_code;
        if ($this->isRedisConnect) {
          Redis::hset('checkCode', $unitCode, $schoolData->check_code);
        }
      }

      // 判斷檢核碼與編號是否相符
      if (substr($account, 0, 3) !== $checkCode) {
        throw new CustomException('序號錯誤', 403001, 403);
      }




      /////////////////////////////////// for test
      if ($account == 'test9') {
        // throw new CustomException('帳密錯誤', 403001, 403);
        $data = [];
        switch ($unitCode) {
          case '11':
            $data = [
              'accessToken' => 'accessToken',
              'accessTokenExpireTime' => 9999,
            ];
            break;
          case '22':
            throw new CustomException('序號錯誤', 403001, 403);
            break;
          case '33':
            throw new CustomException('序號已過期', 403001, 403);
            break;
          default:
            throw new CustomException('未知錯誤', 403001, 403);
            break;
        }
      }
      ////////////////////////////////////// test end


      $user = ModelMember::where('account', $account)->first();
      if (empty($user)) {
        throw new CustomException('序號錯誤', 403001, 403);
      }



      // 判斷身分證是否已綁定 是則驗證是否相同 尚未綁定則綁定
      if (empty($user->person_id)) {
        $user->person_id = $id;
      }
      if ($user->person_id != $id) {
        throw new CustomException('身分證錯誤', 403001, 403);
      }

      // 判斷該序號已超過有效期
      if (!empty($user->expire_at) && strtotime($user->expire_at.' 23:59:59') < time()) {
        throw new CustomException('序號已過期', 403001, 403);
      }
      // 判斷該編號已登入且尚未過期 直接回傳當下token不另行更新
      if (!empty($user->access_token) && strtotime($user->access_token_expire_time) >= time()) {
        $data = [
          'accessToken' => $user->access_token,
          'accessTokenExpireTime' => strtotime($user->access_token_expire_time)
        ];
      } else {
        // 更新 token
        // 因此活動設計帳號於活動完成第一個任務後只有3天有效期，故不設置refresh token  並將access token設置為3天有效期
        $access = config('app.EXPIRE_DAYS',3);
        $refresh = config('app.REFRESH_DAYS',5);
        $user->register = 1;
        $user->access_token = str_random(255);
        $user->refresh_token = str_random(255);
        $user->access_token_expire_time = date("Y-m-d", strtotime("+{$access} days")) . ' 23:59:59';
        $user->refresh_token_expire_time = date("Y-m-d", strtotime("+{$refresh} days")) . ' 23:59:59';
        $user->updated_at = date('Y-m-d H:i:s');
        $user->register_at = date('Y-m-d H:i:s');
        $user->save();
        $data = [
          'accessToken' => $user->access_token,
          'accessTokenExpireTime' => strtotime($user->access_token_expire_time),
        ];
      }
      return $this->success(['data' => $data]);
    } catch (ConnectionException $e) {
      return $this->error(['msgcode' => 401001, 'msg' => '請檢查redis相關設定'], 401);
    } catch (CustomException $ex) {
      return $this->error(['msgcode' => $ex->getCustomCode(), 'msg' => $ex->getCustomMessage()], $ex->getStatusCode());
    } catch (Exception $ex) {
      return $this->error(['msg' => '執行階段錯誤：' . $ex->getMessage()], 500);
    }
  }







  // public function check()
  // {

  //   try {
  //     $response = $this->getUser();
  //     if ($response && strtotime('now') <= strtotime($response->access_token_expire_time)) {
  //       return $this->success();
  //     } else {
  //       throw new CustomException('請重新登入', 401001, 401);
  //     }
  //   } catch (CustomException $ex) {
  //     return $this->error(['msgcode' => $ex->getCustomCode(), 'msg' => $ex->getCustomMessage()], $ex->getStatusCode());
  //   } catch (Exception $ex) {
  //     return $this->error(['msg' => '執行階段錯誤：' . $ex->getMessage()], 500);
  //   }
  //   //return $this->success(['msgcode'=>000000,'msg'=>'']);
  // }



  // public function refreshToken(REQUEST $request)
  // {
  //   try {
  //     $data = [];
  //     $refreshToken = empty($request->input('refreshToken')) ? '' : trim($request['refreshToken']);
  //     if ($refreshToken == 0) {
  //       $data = [
  //         'refreshToken' => 'newFreshToken',
  //         'accessToken' => 'newAccessToken',
  //         'accessTokenExpireTime' => 2222,
  //         'refreshTokenExpireTime' => 6666
  //       ];
  //     }
  //     if ($refreshToken == 1) {
  //       throw new CustomException('參數錯誤', 400001, 400);
  //     }
  //     if ($refreshToken == 2) {
  //       throw new CustomException('驗證失數', 403001, 403);
  //     }
  //     // $validate = new WebModel\member;
  //     // $response = $this->ruleValidator([
  //     //   'refreshToken' => $refreshToken
  //     // ], $validate->rules(['refreshToken']), $validate->rulesMessage());
  //     // if ($response) {
  //     //   throw new CustomException($response, 400001, 400);
  //     // }
  //     $user = ModelMember::where('refresh_token', $refreshToken)->first();
  //     if (empty($user)) {
  //       throw new CustomException('查無編號', 403002, 403);
  //     }

  //     $access = config('app.access_token_expire_hours');
  //     $user->access_token = str_random(255);
  //     $user->refresh_token = str_random(255);
  //     $user->access_token_expire_time = date("Y-m-d H:i:s", strtotime("+{$access} hours"));
  //     $user->refresh_token_expire_time = date("Y-m-d H:i:s", strtotime("+{$access} hours"));
  //     $user->updated_at = date('Y-m-d H:i:s');
  //     $user->save();

  //     $data = [
  //       'accessToken' => $user->access_token,
  //       'refreshToken' => $user->refresh_token,
  //       'accessTokenExpireTime' => strtotime($user->access_token_expire_time),
  //       'refreshTokenExpireTime' => strtotime($user->refresh_token_expire_time)
  //     ];

  //     return $this->success(['data' => $data]);
  //   } catch (CustomException $ex) {
  //     return $this->error(['msgcode' => $ex->getCustomCode(), 'msg' => $ex->getCustomMessage()], $ex->getStatusCode());
  //   } catch (Exception $ex) {
  //     return $this->error(['msg' => '執行階段錯誤：' . $ex->getMessage()], 500);
  //   }
  // }


  // public function logout()
  // {
  //   try {
  //     $member = new WebLib\Member();
  //     $member->logout();
  //     return $this->success(['msg' => '登出成功']);
  //   } catch (CustomException $ex) {
  //     return $this->error(['msgcode' => $ex->getCustomCode(), 'msg' => $ex->getCustomMessage()], $ex->getStatusCode());
  //   } catch (Exception $ex) {
  //     return $this->error(['msg' => '執行階段錯誤：' . $ex->getMessage()], 500);
  //   }
  // }


  public function checkUnitCode($code = '')
  {
    try {
      if (empty($code)) {
        throw new CustomException("單位代碼必填", 400001, 400);
      }
      if ($code == config('app.REDIS_IS_NOT_CONNECTED')) {
        throw new CustomException('請檢查redis相關設定', 401001, 401);
      }
      if (empty(Redis::hget('checkCode', $code))) {
        $checkData = School::active()->select('check_code')->where('school_code', $code)->first();
        if (empty($checkData) || empty($checkData->check_code)) {
          throw new CustomException('單位代碼錯誤', 403001, 403);
        }

        $checkCode = $checkData->check_code;
        Redis::hset('checkCode', $code, $checkCode);
      } else {
        $checkCode = Redis::hget('checkCode', $code);
      }

      return $this->returndata(['data' => $checkCode]);
    } catch (ConnectionException $e) {
      return $this->error(['msgcode' => 401001, 'msg' => '請檢查redis相關設定'], 401);
    } catch (CustomException $ex) {
      return $this->error(['msgcode' => $ex->getCustomCode(), 'msg' => $ex->getCustomMessage()], $ex->getStatusCode());
    } catch (Exception $ex) {

      return $this->error(['msg' => '執行階段錯誤：' . $ex->getMessage()], 500);
    }
  }







  private function getUser()
  {
    return Auth::user();
  }
}
