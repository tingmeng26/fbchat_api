<?php


$api = app('Dingo\Api\Routing\Router');


$api->version('v1', ['namespace' => 'App\Http\Controllers\V1'], function ($api) {
  $api->get('/', function () {
    return env('API_VERSION');
  });
  $api->post('check', 'Fb@processMessage');
  $api->get('check', 'Fb@checkFbSetting');
  $api->get('test','Fb@test');
});
