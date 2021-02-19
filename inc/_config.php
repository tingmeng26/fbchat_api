<?php
ini_set('display_errors', 1);   # 0不顯示 1顯示
error_reporting(E_ALL);         # report all errors
date_default_timezone_set("Asia/Taipei");
mb_internal_encoding("UTF-8");

ini_set("magic_quotes_runtime", 0);

$iCache_ExpireHour=24;//小時
$null_date='-0001-11-30';
//$null_date='1999-11-30';
/*$slash = (strstr(dirname(__FILE__), '/'))?"/":"\\";
define("CONFIG_DIR",dirname(__FILE__).$slash);*/
define("CONFIG_DIR",dirname(__FILE__).DIRECTORY_SEPARATOR);
define("ROOT_DIR",substr(CONFIG_DIR, 0, strpos(CONFIG_DIR, DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR)+1));
define("DEF_LANG","tw");

ob_start();
//↓↓↓↓↓↓↓↓↓↓此區請依主機實際狀況修正↓↓↓↓↓↓↓↓↓↓↓↓
require_once(CONFIG_DIR."_cusconfig.php");
//↑↑↑↑↑↑↑↑↑此區請依主機實際狀況更動↑↑↑↑↑↑↑↑↑↑↑↑↑

if (!isset($_SESSION)){
    session_set_cookie_params(0,$weburl_cookiepath,$web_domain); 
    session_start();
}

header("Content-type: text/html; charset=utf-8");

include '_configparameter.php';

require_once(CONFIG_DIR."_func.php");
require_once(CONFIG_DIR."_func_cache.php");
require_once(CONFIG_DIR."_errormsg.php");
require_once(CONFIG_DIR."_database.class.php");
require_once(CONFIG_DIR."_func_smtp.php");

/*FB*/

//lib的autoload
//採用spl_autoload_register載入(用__autoload會跟phpmailer的spl_autoload_register衝突)
function incautoload($classname){
    if(strlen($classname)>9 && mb_substr(strtolower($classname), 0, 9)=='phpexcel_'){
        return false;
    }
    $filename = '';
    if(strlen($classname)>6 && (mb_substr($classname, 0, 6)=='class_' || mb_substr($classname, 0, 8)=='classdb_')){
        $filename = CONFIG_DIR . "class/" . strtolower($classname) . ".php";
    }
    else if(strlen($classname)>5 && mb_substr($classname, 0, 5)=='coder'){
        $filename = CONFIG_DIR . "lib/" . strtolower($classname) . ".php";
    }
    if($filename!=''){
        if ( file_exists($filename) ){
            include_once $filename;
        }else{
            echo 'notfound:'.$filename;
        }
    }
}

// if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
//     //SPL autoloading was introduced in PHP 5.1.2
//     if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
//         spl_autoload_register('incautoload', true, true);
//     } else {
//         spl_autoload_register('incautoload');
//     }
// } else {
//     function __autoload($classname)
//     {
//         incautoload($classname);
//     }
// }
spl_autoload_register('incautoload', true, true);

?>