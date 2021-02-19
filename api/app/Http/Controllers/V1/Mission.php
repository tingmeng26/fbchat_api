<?php

namespace App\Http\Controllers\V1;

use \App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Model as WebModel;
use App\Model\ExtraLotteryPool;
use App\Model\LotteryPool;
use App\Model\Mission as ModelMission;
use App\WebLib as WebLib;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class Mission extends Controller
{
  #region [check Swagger note]
  /**
   * @SWG\Get(
   *     path="/mission",
   *     tags={"APP - 任務狀態"},
   *     description="任務(1=漫畫2=動畫3=音樂4=畫作5=音樂演奏)及彩蛋 extra 1234 完成狀態",
   *     summary = "任務完成狀態",
   *     security = {{
   *     	"MyHeaderAuthentication-Authorization":{}
   *     }},
   *     @SWG\Response(
   *         response = "200",
   *         description = "SUCCESS",
   *         @SWG\Schema(
   *               @SWG\Property(
   *                   property="success",
   *                   type="boolean",
   *                   description="回傳結果(true|false)"
   *               ),
   * *                    @SWG\Property(property="data", type="object",
   * *                      @SWG\Property(property="mission1", type="boolean",description="true 已完成 false尚未完成"),
   * *                      @SWG\Property(property="mission2", type="boolean",description="true 已完成 false尚未完成"),
   * *                      @SWG\Property(property="mission3", type="boolean",description="true 已完成 false尚未完成"),
   * *                      @SWG\Property(property="mission4", type="boolean",description="true 已完成 false尚未完成"),
   * *                      @SWG\Property(property="mission5", type="boolean",description="true 已完成 false尚未完成"),
   * *                      @SWG\Property(property="extra1", type="boolean",description="true 已完成 false尚未完成"),
   * *                      @SWG\Property(property="extra2", type="boolean",description="true 已完成 false尚未完成"),
   * *                      @SWG\Property(property="extra3", type="boolean",description="true 已完成 false尚未完成"),
   * *                      @SWG\Property(property="extra4", type="boolean",description="true 已完成 false尚未完成"),
   *                    ),
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

  public function getMissionList()
  {

    try {
      $user = $this->getUser();
      $mission = ModelMission::where('m_id', $user->id)->first();
      $data = [
        'mission1' => false,
        'mission2' => false,
        'mission3' => false,
        'mission4' => false,
        'mission5' => false,
        'extra1' => false,
        'extra2' => false,
        'extra3' => false,
        'extra4' => false,
      ];
      if (!empty($mission)) {
        $data = [
          'mission1' => empty($mission['mission1']) ? false : true,
          'mission2' => empty($mission['mission2']) ? false : true,
          'mission3' => empty($mission['mission3']) ? false : true,
          'mission4' => empty($mission['mission4']) ? false : true,
          'mission5' => empty($mission['mission5']) ? false : true,
          'extra1' => empty($mission['extra1']) ? false : true,
          'extra2' => empty($mission['extra2']) ? false : true,
          'extra3' => empty($mission['extra3']) ? false : true,
          'extra4' => empty($mission['extra4']) ? false : true,
        ];
      }

      return $this->success(['data' => $data]);
    } catch (CustomException $ex) {
      return $this->error(['msgcode' => $ex->getCustomCode(), 'msg' => $ex->getCustomMessage()], $ex->getStatusCode());
    } catch (Exception $ex) {
      return $this->error(['msg' => '執行階段錯誤：' . $ex->getMessage()], 500);
    }
    //return $this->success(['msgcode'=>000000,'msg'=>'']);
  }


  #region [Login Swagger note]
  /**
   * @SWG\Put(
   *     path="/mission/update",
   *     tags={"APP - 任務狀態"},
   *     description="更新任務  type 帶11 測試403參數錯誤  22測試200更新成功
   *        1 = 漫畫 2=動畫 3=音樂 4=畫作 5=音樂演奏 6=彩蛋。任務更新不需依照順序，第一次任務完成會替該帳號設定D+3 有效期，過期則無法登入
   *      狀態代碼(000000為正常，400001為欄位不齊全，403001為資料驗證失敗 )",
   *     summary = "更新任務",
   *     security = {{
   *     	"MyHeaderAuthentication-Authorization":{}
   *     }},
   *     @SWG\Parameter(
   *          name="type",
   *         in="formData",
   *         description="1 = 漫畫 2=動畫 3=音樂 4=畫作 5=音樂演奏 6=彩蛋1 7=彩蛋2 8=彩蛋3 9=彩蛋4",
   *         type="integer",
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
  public function updateMission(REQUEST $request)
  {
    try {
      $data = $request->all();
      $type = empty($data['type']) ? '' : trim($data['type']);
      switch ($type) {
        case '11':
          throw new CustomException('參數錯誤', 403001, 403);
          break;
        case '22':
          return $this->success(['msg' => '更新成功']);
          break;
      }


      $mission = new WebModel\Mission();
      $response = $this->ruleValidator(['type' => $type], $mission->rules(['type']), $mission->rulesMessage());
      if ($response) {
        throw new CustomException($response, 403001, 403);
      }
      $user = $this->getUser();

      // 先檢查序號是否過期
      if (!empty($user->expire_at) && strtotime($user->expire_at.' 23:59:59') < time()) {
        throw new CustomException('序號已過期', 401001, 401);
      }


      $mission = ModelMission::where('m_id', $user->id)->first();

      // 判斷若全任務及彩蛋均完成 不需進行後續操作
      if (!empty($mission) && $mission->all_mission > 0 && $mission->all_extra > 0) {
        return $this->returndata(['msg' => '任務均已完成']);
      }
      // 無資料表示首次完成
      if (!empty($mission)) {
        switch ($type) {
          case 1:
            $mission->mission1 = 1;
            break;
          case 2:
            $mission->mission2 = 1;
            break;
          case 3:
            $mission->mission3 = 1;
            break;
          case 4:
            $mission->mission4 = 1;
            break;
          case 5:
            $mission->mission5 = 1;
            break;
          case 6:
            if ($mission->all_mission != 1) {
              throw new CustomException('請先完成其他任務', 403001, 403);
            }
            $mission->extra1 = 1;

            break;
          case 7:
            if ($mission->all_mission != 1) {
              throw new CustomException('請先完成其他任務', 403001, 403);
            }
            $mission->extra2 = 1;
            break;
          case 8:
            if ($mission->all_mission != 1) {
              throw new CustomException('請先完成其他任務', 403001, 403);
            }
            $mission->extra3 = 1;
            break;
          case 9:
            if ($mission->all_mission != 1) {
              throw new CustomException('請先完成其他任務', 403001, 403);
            }
            $mission->extra4 = 1;
            break;
        }
        // 判斷完成任務後是否5項全完成
        if ($mission->mission1 > 0 && $mission->mission2 > 0 && $mission->mission3 > 0 && $mission->mission4 > 0 && $mission->mission5 > 0 && $mission->all_mission == 0) {
          $mission->all_mission = 1;
          $lotteryId = LotteryPool::insertGetId([
            'm_id' => $user->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
          ]);
          if (empty($lotteryId)) {
            throw new CustomException('取得抽獎資格失敗', 403001, 403);
          }
        }
        // 判斷所有彩蛋均完成  寫入彩蛋獎抽獎資格
        if ($mission->extra1 > 0 && $mission->extra2 > 0 && $mission->extra3 > 0 && $mission->extra4 > 0) {
          $mission->all_extra = 1;
          $extraId = ExtraLotteryPool::insertGetId([
            'm_id' => $user->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
          ]);
          if (empty($extraId)) {
            throw new CustomException('取得彩蛋抽獎資格失敗', 403001, 403);
          }
        }
        $mission->save();
      } else {
        // 任務 1~5完成之前不可完成彩蛋
        if ($type > 5) {
          throw new CustomException('請先完成活動任務', 403001, 403);
        }
        $id = ModelMission::insertGetId([
          'm_id' => $user->id,
          'mission' . $type => 1,
          'created_at' => Carbon::now(),
          'updated_at' => Carbon::now(),
        ]);
        if (empty($id)) {
          throw new CustomException('設定任務失敗', 403001, 403);
        }
        // 完成首項任務後替該序號設定D+3到期日 並更新access expire time
        $expire = config('app.EXPIRE_DAYS',3);
        $user->expire_at = date('Y-m-d', strtotime("+{$expire} days"));
        $user->access_token_expire_time = date("Y-m-d", strtotime("+{$expire} days")) . ' 23:59:59';
        $user->save();
      }

      return $this->returndata(['msg' => '任務設定成功']);
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
