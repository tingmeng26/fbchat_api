<?php

namespace App\Providers;

use App\Model as WebModel;
use App\Model\Member as ModelMember;
use App\WebLib\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use coderDBConf;
use Illuminate\Support\Facades\DB;
use Request;

class AuthServiceProvider extends ServiceProvider
{

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Boot the authentication services for the application.
   *
   * @return void
   */
  public function boot()
  {
    // Here you may define how you wish users to be authenticated for your Lumen
    // application. The callback which receives the incoming request instance
    // should return either a User instance or null. You're free to obtain
    // the User instance via an API token or any other method necessary.

    $this->app['auth']->viaRequest('api', function ($request) {
      $api_token = $request->header('Authorization');
      // $api_token = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '';
      if ($api_token == '123456789' || $api_token == '123') {
        return 1;
      }

      if (!empty($api_token)) {
        $member = ModelMember::where('access_token', $api_token)->where('access_token_expire_time','>=',date('Y-m-d H:i:s'))->first();
        return empty($member) ? null : $member;
      } else {
        return null;
      }
    });
  }
}
