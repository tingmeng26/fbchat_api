<?php //程式人員設定檔
if (!empty($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == 'localhost') && (php_uname('n') == 'DESKTOP-6F75QS5' || php_uname('n') == 'linziyude-MacBook-Pro.local')) {
  $nowHost = 'joanne';
} elseif (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == '59.126.17.211') {
  $nowHost = 'ServerCoder';
} elseif (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == '35.194.183.84') {
  $nowHost = 'gcp';
} else {
  $nowHost = 'mark';
}
switch ($nowHost) {
  case 'gcp':
    $web_domain = "l35.194.183.84";
    $web_root = "/beethoven/"; //前台cookie紀錄路徑

    $HS = "127.0.0.1";
    $ID = "root";
    $PW = "123456";
    $DB = "beethoven";
    $HS_read = "127.0.0.1";
    $ID_read = "root";
    $PW_read = "123456";
    $DB_read = "beethoven";
    $redis_host = 'localhost';
    $redis_port = 6379;
    break;

  case 'ServerCoder':
    $web_domain = "59.126.17.211";
    $web_port = "8082"; //port號
    $web_root = "/ski/"; //前台cookie紀錄路徑

    $HS = "localhost";
    $ID = "root";
    $PW = "Toysroom8";
    $DB = "ski";

    $HS_read = "127.0.0.1";
    $ID_read = "root";
    $PW_read = "Toysroom8";
    $DB_read = "ski";

    /*Mongo Database 資料庫*/
    $Mongo_HS = "localhost";
    $Mongo_DB = 'Toysroom8';

    $node_port = ':9003/';
    break;
  default:
    $web_domain = "localhost";
    $web_root = "/beethoven/"; //前台cookie紀錄路徑

    $HS = "127.0.0.1";
    $ID = "root";
    $PW = "";
    $DB = "beethoven";

    $HS_read = "127.0.0.1";
    $ID_read = "root";
    $PW_read = "";
    $DB_read = "beethoven";
    $redis_host = 'localhost';
    $redis_port = 6379;
    break;
}
