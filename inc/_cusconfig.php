<?php //上線設定檔
$webname = "貝多芬250年線上活動"; //網站名稱
$nowHost = '';
/*Database 資料庫*/
if (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'beethoven.show') {
  $nowHost = 'production';
} else {
  //開發人員設定檔
  include '_localconfig.php';
}
switch ($nowHost) {
  case 'production':
    $web_domain = "beethoven.show";
    $web_root = "/"; //前台cookie紀錄路徑

    $HS = "localhost";
    $ID = "beethoven";
    $PW = "V4r7w%s8";
    $DB = "beethoven";
    $HS_read = "localhost";
    $ID_read = "beethoven";
    $PW_read = "V4r7w%s8";
    $DB_read = "beethoven";
    $redis_host = 'localhost';
    $redis_port = 6379;
    break;
}

$weburl_cookiepath = $web_root; //前台cookie紀錄路徑 ex.'/'
$webmanageurl_cookiepath = $web_root . 'Web_Manage' . "/"; //後台cookie紀錄路徑 ex.'/Web_Manage/'
$session_domain = $web_domain; //允許seesion儲存的網域
$weburl = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http') . "://" . $web_domain . (!empty($web_port) ? ':' . $web_port : '') . $web_root; //網址

/*Email(系統發信的寄件人)*/
$sys_email = "donoreply@skatingfun.nox.com.tw";
$sys_name = "中信智動GO";

/*SMTP Server*/
$smtp_isSMTP = true;
$smtp_auth = true;
$smtp_host = "ssl://smtp.gmail.com";
$smtp_port = 465;
// $smtp_id = "yangsspeedreading@gmail.com"; //email帳號
// $smtp_pw = "A23314141"; //email密碼
//--------------------
$smtp_id = "infomail.yangs@gmail.com"; //email帳號
$smtp_pw = "yangs168@@"; //email密碼

// $smtp_id = 'iheartmailservice@gmail.com';
// $smtp_pw = 'ykdqvejkyexdifix';
//--------------------
$smtp_secure = true;
