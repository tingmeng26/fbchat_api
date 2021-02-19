<?php

use App\Model\Member;
use Faker\Factory;
use Illuminate\Support\Facades\App;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class MemberTest extends TestCase
{

  /**
   * 測試透過縣市代碼取得學校
   */
  public function testGetSchool()
  {
    $this->get('/api/school/1');
    $this->seeStatusCode(403);
    // 測試 redis 服務無啟用
    $this->get('/api/school/REDIS_IS_NOT_CONNECTED');
    $this->seeStatusCode(401);
    // 測試存在的單位代碼 94 為 通路  回傳第一筆為 711 檢核碼 711
    $result = $this->get('/api/school/94');
    $result->seeStatusCode(200);
    $result->assertEquals('711', $result->response->original['data'][0]['checkCode']);
  }

  public function testLogin(){
    $this->post('/api/login');
    $this->seeStatusCode(400);
  }
}
