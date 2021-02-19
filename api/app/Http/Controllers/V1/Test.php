<?php

namespace App\Http\Controllers\V1;

use \App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Model as WebModel;
use App\Model\Member;
use App\Model\School;
use App\WebLib as WebLib;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Predis\Connection\ConnectionException;

class Test extends Controller
{








  public function test()
  {
    try {





      // $data = School::get();
      // foreach ($data as $row) {
      //   $number = $row->number + 100;
      //   $count = Member::where('s_id', $row->id)->count();
      //   while ($count < $number) {
      //     $string = str_shuffle('abcdefghjkmnpqrstuvwxyz123456789');
      //     $code = substr($string, 0, 5);
      //     Member::insert([
      //       's_id' => $row->id,
      //       'account' => $row->check_code . $code,
      //       'is_public' => 1,
      //     ]);
      //     $count = Member::where('s_id', $row->id)->count();
      //   }
      // }

      $data = School::where('school_type', 'o')->get();
      foreach ($data as $row) {
        $count = Member::where('s_id', $row->id)->count();
        if ($count != $row->number + 100) {
          var_dump($row->id);
          exit;
        }
      }
      return $this->returndata(['data' => 'helo']);
    } catch (CustomException $ex) {
      return $this->error(['msgcode' => $ex->getCustomCode(), 'msg' => $ex->getCustomMessage()], $ex->getStatusCode());
    } catch (Exception $ex) {
      return $this->error(['msg' => '執行階段錯誤：' . $ex->getMessage()], 500);
    }
    //return $this->success(['msgcode'=>000000,'msg'=>'']);
  }

  public function redis()
  {
    try {
      // 總數 = 5
      $store = 5;
      Redis::watch($store);
      $sales = Redis::get('sales');
      if ($sales >= $store) {
        var_dump('售完');
        exit;
      }
      Redis::multi();
      Redis::incr('sales');
      Redis::hset('user_list', 'user_id_' . mt_rand(1, 9999), time());
      $res = Redis::exec();
      if ($res) {
        $userList = Redis::hGetAll("user_list");
        echo "抢购成功！<br/>";
        echo "剩余数量：" . ($store - $sales - 1) . "<br/>";
        echo "用户列表：<pre>";
        var_dump($userList);
      }
      exit;
    } catch (ConnectionException $e) {
      var_dump(123);
      exit;
    }
    // $array = [
    //   'name'=>'mark',
    //   'gender'=>'male',
    //   'height'=>"5'11"
    // ];
    // return $this->returndata(['data'=>$data]);
  }

  public function huge()
  {
    // $data = Member::take(200000)->get()->toArray();
    $sql = "SELECT * FROM members AS a JOIN ( SELECT MAX( ID ) AS ID FROM members) AS b ON ( a.ID >= FLOOR( b.ID * RAND( ) ) ) LIMIT 1";
    $data = DB::select($sql);
    var_dump($data);
    exit;

    // 測試1萬筆佔用空間
    // $sql = "select account from members limit 0,10000";
    // $data = DB::select($sql);
    // foreach($data as $row){
    //   Redis::hset('test',$row->account,1);
    // }
    // var_dump(123);exit;
  }

  public function testRedis()
  {
    return $this->returndata(['data' => $this->isRedisConnect]);
  }
}
