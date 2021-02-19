<?php
require __DIR__ . '/../../inc/_cusconfig.php';
return [

  /*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| stdClass object; however, you may desire to retrieve records in an
	| array format for simplicity. Here you can tweak the fetch style.
	|
	*/

  'fetch' => PDO::FETCH_CLASS,

  /*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

  'default' => env('DB_CONNECTION', 'mysql'),


  'connections' => [
    //Default configuration
    'mysql' => [
      'read' => [
        'host' => $HS_read
      ],
      'write' => [
        'host' => $HS
      ],
      'driver'    => 'mysql',
      'database'  => $DB,
      'username'  => $ID,
      'password'  => $PW,
      'charset'   => 'utf8',
      'collation' => 'utf8_unicode_ci',
      'prefix'    => '',
      'timezone' => env('DB_TIMEZONE', '+08:00'),
    ],
  ],

  'redis' => [
    'client' => 'predis', // 如果要使用 predis，可以從此處設定
    'cluster' => false,
    'default' => [
      'host'     => env('REDIS_HOST','127.0.0.1'),
      'password' => env('REDIS_PASSWORD', null),
      'port'     => env('REDIS_PORT', 6379),
      'database' => 0,
    ],
  ],

  /*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

  'migrations' => 'migrations',
];
