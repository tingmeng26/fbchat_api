<?php


$api = app('Dingo\Api\Routing\Router');


$api->version('v1', ['namespace' => 'App\Http\Controllers\V1'], function ($api) {
  $api->get('/', function () {
    return env('API_VERSION');
  });
  $api->get('version', 'system@version');
 
  $api->post('bbb','Member@bbb');
  $api->get('check','Fb@checkFbSetting');

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
