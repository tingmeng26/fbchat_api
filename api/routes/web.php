<?php

/* $router->get('/key', function() {
  return str_random(32); //生成一串隨機碼，放在.env的KEY
  });
 */

$api = app('Dingo\Api\Routing\Router');


$api->version('v1', ['namespace' => 'App\Http\Controllers\V1'], function ($api) {
  $api->get('/', function () {
    return env('API_VERSION');
  });
  $api->get('version', 'system@version');
  // $api->get('test','Test@test');
  // $api->get('redis','Test@redis');
  // $api->get('huge','Test@huge');
  // 測試 redis 連線情況
  $api->get('testRedis', 'Test@testRedis');
  // 取得縣市
  $api->get('city', 'Member@getCity');
  // 取得學校
  $api->get('school/{code}', 'Member@getSchool');
  // 取得學校檢核碼
  $api->get('unit/check/{code}', 'Member@checkUnitCode');
  // 登入
  $api->post('login', 'Member@login');

  // 重置
  $api->post('reset', 'Member@reset');
  // 重新取得 refresh token
  // $api->post('refresh', 'Member@refreshToken');

  $api->group(['middleware' => 'authToken'], function () use ($api) {
    // 登出
    // $api->delete('logout', 'Member@logout');
    // check token 
    // $api->get('check','Member@check');
    // 取得任務情形
    $api->get('mission', 'Mission@getMissionList');
    // 更新任務
    $api->put('mission/update', 'Mission@updateMission');
  });
});
