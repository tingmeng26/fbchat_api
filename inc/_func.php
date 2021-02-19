<?php
//=============================================//
//輸入

//request GET及POST值
function request_pag($name = "page") {
    $page = request_num($name);
    if ($page == "") {
        return 1;
    }
    else if ($page < 1) {
        return 1;
    }
    else {
        return $page;
    }
}

function request_str($name) {
    $value = request($name);

    //如果magie_qutes_gpc 為ON ，則恢復
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    return $value;
}

function request_num($name) {
    $value = request($name);

    //如果magie_qutes_gpc 為ON ，則恢復
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }

    if (is_numeric($value)) {
        return $value;
    }
    else {
        return "0";
    }
}

function request_ary($name, $_key = 1) {
    $data = request($name);

    if (gettype($data) == "array") {
        $d = array();
        foreach ($data as $key => $value) {

            $v = stripslashes($value);

            if ($_key == 1) {
                $v = hc($v);
            }
            else if ($_key == 0) {
                $v = intval($v);
            }
            else if ($_key == 3){
                $v = floatval($v);
            }
            $d[$key] = $v;
        }

        return $d;
    }
    else {
        return array();
    }
}

function request_ip() {
    return $_SERVER["REMOTE_ADDR"];
}

function request_cd() {
    return datetime();
}

function request_basename() {
    $path = basename($_SERVER['REQUEST_URI']);
    $last_index = strpos($path, '?');
    return $last_index > 0 ? substr($path, 0, $last_index) : $path;
}

function request_url() {

    return "http://" . dirname($_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"]) . "/";
}

function request_weburl() {

    return "http://" . $_SERVER["HTTP_HOST"] . "/";
}

function getCurrentUrl(){
    global $_SERVER;
    $php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);
    if(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on'){
        $protocol = 'https://';
    }else{
        $protocol = 'http://';
    }
    $host = $_SERVER['HTTP_HOST'];
    if($_SERVER['SERVER_PORT'] != '' && (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
        $port = ':' . $_SERVER['SERVER_PORT'];
    }else{
        $port = '';
    }
    return $protocol . $host . $port . $php_request_uri;
}

function request_ref() {
    return $_SERVER["http_referer"];
}

function request_date($name, $default = "") {
    $value = request($name);

    //如果magie_qutes_gpc 為ON ，則恢復
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }

    if (strlen($value) >= 8) {
        $dat = strtotime($value);
        if ($dat) {
            return $value;
        }
        else {
            return $default;
        }
    }
    else {
        return $default;
    }
}

function request($name) {
    $value = "";
    if (isset($_GET[$name])) {
        $value = $_GET[$name];
    }
    else if (isset($_POST[$name])) {
        $value = $_POST[$name];
    }
    else {
        $value = "";
    }
    return $value;
}
function get($str, $type = 0,$onlyrenulls = false) {
    if (!isset($_GET[$str])) return "";
    $gstr = trim($_GET[$str]);
    switch ($type) {
        case 0:
            if($onlyrenulls && $gstr=='')return "";
            return intval($gstr);
            break;

        case 1:
            if (!get_magic_quotes_gpc()) {
                $gstr = addslashes($gstr);
            }
            $gstr = htmlspecialchars($gstr);
            return $gstr;
            break;

        case 3:
             //float
            return floatval($gstr);
            break;
    }
}
function post($str, $type = 0) {
    if (!isset($_POST[$str])) return "";
    $gstr = trim($_POST[$str]);
    switch ($type) {
        case 0:
            return intval($gstr);
            break;

        case 1:
            $gstr = htmlspecialchars($gstr);
            if (!get_magic_quotes_gpc()) {
                $gstr = addslashes($gstr);
            }
            return trim($gstr);
            break;

        case 2:
             //admin

            if (get_magic_quotes_gpc()) {
                $gstr = stripslashes($gstr);
            }
            return trim($gstr);
            break;

        case 3:
             //float
            return floatval($gstr);
            break;
        case 4:
            //轉化特定符號，取消反斜線
            $gstr = htmlspecialchars($gstr,ENT_QUOTES);
            if (get_magic_quotes_gpc()) {
                $gstr = stripslashes($gstr);
            }
            return trim($gstr);
            break;
    }
}

//=============================================//
//輸出
function hc($str) {
    //輸出字串並將html字符編碼
    return htmlentities($str, ENT_QUOTES, "UTF-8");
}

function uc($str) {
    //url字符編碼
    return urlencode($str);
}

function sc($str) {
    $str = str_replace("\r", "\\r", $str);
    $str = str_replace("\n", "\\n", $str);
    $str = addslashes(str_replace("\"", "''", $str));
    return $str;
}
function br($str) {
    return preg_replace("/(\015\012)|(\015)|(\012)/", "<br/>", $str);
}
function removebr($str) {
    return preg_replace("/<br[[:space:]]*\/?[[:space:]]*>/", "\015\012", $str);
}
function addLink($str,$class='') {//將文字內網址加上超連結
    $str = preg_replace('#(http|https|ftp|telnet)://([0-9a-z\.\-]+)(:?[0-9]*)([0-9a-z\_\/\?\&\=\%\.\;\#\-\~\+]*)#i','<a href="\1://\2\3\4" class="'.$class.'" target="_blank">\1://\2\3\4</a>', $str);
    return $str;
}

function tc_left($s, $c) {
    if (mb_strlen($s) > $c) {
        return left($s, $c) . "…";
    }
    else {
        return $s;
    }
}

function GBsubstr($string, $start, $length) {
    $beginIndex = $start;
    if (strlen($string) < $start) {
        return "";
    }
    if (strlen($string) < $length) {

        return substr($string, $beginIndex);
    }

    $char = ord($string[$beginIndex + $length - 1]);
    if ($char >= 224 && $char <= 239) {
        $str = substr($string, $beginIndex, $length - 1) . "...";
        return $str;
    }

    $char = ord($string[$beginIndex + $length - 2]);
    if ($char >= 224 && $char <= 239) {
        $str = substr($string, $beginIndex, $length - 2) . "...";
        return $str;
    }

    return substr($string, $beginIndex, $length) . "...";
}


//做unescape的處理
function phpUnescape($escstr) {
    preg_match_all("/%u[0-9A-Za-z]{4}|%.{2}|[0-9a-zA-Z.+-_]+/", $escstr, $matches);
    $ar = & $matches[0];
    $c = "";
    foreach ($ar as $val) {
        if (substr($val, 0, 1) != "%") {
            $c.= $val;
        }
        elseif (substr($val, 1, 1) != "u") {
            $x = hexdec(substr($val, 1, 2));
            $c.= chr($x);
        }
        else {
            $val = intval(substr($val, 2), 16);
            if ($val < 0x7F)
             // 0000-007F
            {
                $c.= chr($val);
            }
            elseif ($val < 0x800)
             // 0080-0800
            {
                $c.= chr(0xC0 | ($val / 64));
                $c.= chr(0x80 | ($val % 64));
            }
            else

            // 0800-FFFF
            {
                $c.= chr(0xE0 | (($val / 64) / 64));
                $c.= chr(0x80 | (($val / 64) % 64));
                $c.= chr(0x80 | ($val % 64));
            }
        }
    }
    return $c;
}

//=============================================//
//字串
function right($value, $count) {
    return mb_substr($value, ($count * -1));
}

function left($string, $count) {
    return mb_substr($string, 0, $count);
}

//取的指定長度的英數亂碼字串
function generatorPassword($password_len = 5) {
    $password = '';
    $word = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789';
    $len = strlen($word);

    for ($i = 0; $i < $password_len; $i++) {
        $password.= $word[rand() % $len];
    }
    return $password;
}

//字串隱碼處理(*)
//(字串,替代字,保留(前面數來幾位),保留(後面數來幾位),是否要隱碼處理)
function str_makehide($string,$symbol,$begin_num = 0,$end_num = 0,$strhide=true){
    if(!$strhide || $string==''){
        return $string;
    }
    $string_length = mb_strlen($string);
    $begin_num = (int)$begin_num;
    $end_num = (int)$end_num;
    $string_middle = '';

    if(($begin_num + $end_num) >= $string_length){
        return $string_length;
    }

    $symbol_num = $string_length - ($begin_num + $end_num);
    $string_begin = mb_substr($string, 0,$begin_num);
    $string_end = mb_substr($string, $string_length-$end_num);

    for ($i=0; $i < $symbol_num; $i++) {
        $string_middle .= $symbol;
    }
    return $string_begin.$string_middle.$string_end;
}

//網址
function chkUrl($str) {
    if (substr($str, 0, 7) != 'http://') {
        $str = 'http://' . $str;
    }
    return $str;
}
//=============================================//
//Session

//取得session
function session($name) {
    if (isset($_SESSION[$name]) || !empty($_SESSION[$name])) {
        return $_SESSION[$name];
    }
    else {
        return "";
    }
}
//取得session
function getSession($name) {
    return session($name);
}
//設定session
function setSession($name, $value) {
    $_SESSION[$name] = $value;
}
//清除session
function unSession($name) {
    unset($_SESSION["$name"]);
}

//=============================================//
//cookie

//取得cookie
function cookie($name) {
    if (isset($_COOKIE[$name]) || !empty($_COOKIE[$name])) {
        return phpUnescape($_COOKIE[$name]);
    }
    else {
        return "";
    }
}
//取得cookie
function getCookie($name) {
    return cookie($name);
}
//清除cookie
function unCookie($name, $path = "") {
    setcookie($name, "", time() - 60*60*24*365,$path);
}
function saveCookieHour($name, $val, $h, $path = "/",$httponly=false) {
    $expire = time() + $h * 60 * 60;
    unCookie($name);
    setcookie($name, urlencode($val), $expire, $path,"",false,$httponly);//exit;
}

function saveCookie($name, $val, $path = "/",$httponly=false) {
    global $iCookMainExpireDay;
    $expire = time() + $iCookMainExpireDay * 24 * 60 * 60;
    unCookie($name);
    setcookie($name, urlencode($val), $expire, $path,"",false,$httponly);
}

//=============================================//
//日期時間
function DateDiff($d1, $d2 = "now") {
    if (is_string($d1)) $d1 = strtotime($d1);
    if (is_string($d2)) $d2 = strtotime($d2);
    return ($d2 - $d1) / 86400;
}

function datetime($form = "Y/m/d H:i:s", $value = "now") {

    //Y/m/d H:i:s
    return date($form, strtotime($value));
}

function datetime_addMin($form = "Y/m/d H:i:s", $value = "now", $second = 0) {

    return datetime($form, $value . " +{$second} minutes");
}
function datetime_addDay($form = "Y/m/d H:i:s", $value = "now", $second = 0) {

    return datetime($form, $value . " +{$second} days");
}
function datetime_addMonth($form = "Y/m/d H:i:s", $value = "now", $second = 0) {

    return datetime($form, $value . " +{$second} month");
}

//民國年->西元年
//$in_type為分隔符號
function dateTo_ad($in_date, $in_type = ""){
    $cyear = substr($in_date, 0, -4);
    $year = ((int)$cyear) + 1911;
    $mon = substr($in_date, -4, 2);
    $day = substr($in_date, -2);
    $date = date("Y" . $in_type . "m" . $in_type . "d", mktime(0, 0, 0, $mon, $day, date($year)));
    return $date;
}

//=============================================//
//檔案
function filesize_check($file, $size = 1000) {
    //檔案大小測試
    if (isset($file)) {
        if ($file["size"] <= 1024 * $size) {
            return true;
        }
    }
    return false;
}

function filename_check($filename, $ext) {
    if ($filename > '') {
        if (in_array(end(explode(".", strtolower($filename))), $ext)) {
            return true;
        }
    }
    return false;
}

function file_ext($file) {
    //傳回副檔名
    $ext = explode('.', $file["name"]);
    $size = count($ext);
    return $ext[$size - 1];
}

function file_open($file) {
    //讀取檔案
    $handle = fopen($file, "r");
    $contents = fread($handle, filesize($file));
    fclose($handle);
    return $contents;
}

function del_file($file){
    if(is_file($file)){
        unlink($file);
    }
}

function autoBuildDir($src){
    $root = ROOT_DIR;
    $array = preg_split('/\//',$src);
    foreach($array as $val){
        if (empty($val)) continue;
        $path = sprintf("%s/%s/",$root,$val);
        file_exists($path) or mkdir($path);
        $root = $path;
    }
    return $root;
}
function safeWriteFile($fileName, $dataToSave, $mode = 'w'){
    if (!file_exists($fileName)) $mode .= "+";
    if ($fp = fopen($fileName, $mode)){
        $startTime = microtime();
        do{
            $canWrite = flock($fp, LOCK_EX);
           // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
           if(!$canWrite) usleep(round(rand(0, 100)*1000));
        } while ((!$canWrite) and ((microtime() - $startTime) < 1000));

        if ($canWrite){
            fwrite($fp, $dataToSave);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return filemtime($fileName);
    }
    return false;
}


function getMimeType( $filename ) { // 檢查檔案類型
    $realpath = realpath( $filename );
    if ( $realpath
            && function_exists( 'finfo_file' )
            && function_exists( 'finfo_open' )
            && defined( 'FILEINFO_MIME_TYPE' )
    ) {
            // Use the Fileinfo PECL extension (PHP 5.3+)
            return finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $realpath );
    }
    if ( function_exists( 'mime_content_type' ) ) {
            // Deprecated in PHP 5.3
            return mime_content_type( $realpath );
    }
    return false;
}

//JS輔助
function script($key, $url = "") {
    if(!empty($key)){
        echo "<script>alert('$key')</script>";
    }
    if ($url != "") {
        echo "<script>window.location='$url'</script>";
        exit;
    }
    else {
        echo "<script>window.history.go(-1)</script>";
        exit;
    }
}
function redirect($url) {
    echo "<script>window.location='$url'</script>";
    exit;
}


//=============================================//
//資料驗證
//空值+長度
$str_message = "";
function isNull($str, $name, $min, $max='') {
    global $str_message;
    if ($max=='' && mb_strlen($str) >= $min) {
        return true;
    }elseif($max!='' && mb_strlen($str) >= $min && mb_strlen($str) <= $max){
        return true;
    }else {
        $str_message = getErrorMsg('rangelength',array('name'=>$name,'maxlength'=>$max,'minlenth'=>$min));//$name ."(".($max!=''?"$max字以内":'').($min>1?"$min字以上":"").")";
        return false;
    }
}

function isRequire($str, $name) {
    global $str_message;
    if (!empty($str)) {
        return true;
    }else {
        $str_message = getErrorMsg('require',array('name'=>$name));
        return false;
    }
}

function isNum($str, $name, $min, $max) {
    global $str_message;
    if (!is_numeric($str)) {
        $str_message = $name . '必須是整數型態';
        return false;
    }
    else {
        if (intval($str) >= $min && intval($str) <= $max) {
            return true;
        }
        else {
            $str_message = $name . '必須大於' . $min . '和小於' . $max;
            return false;
        }
    }
}

function isEmail2($str, $name) {
    global $str_message;
    $result = preg_match("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $str);
    if (!$result) {
        $str_message = getErrorMsg('format',array('name'=>"E-mail"));// $name . '不是合法的Email格式';
    }
    return $result;
}
function isDate($str, $name = "") {
    global $str_message;
    if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $str)) {
        return true;
    }
    else {

        $str_message = $name . '不是合法的日期格式';
        return false;
    }
}
function isphone($str, $name = "") {
    global $str_message;
    if ($str!=='' && preg_match("/^[0][1-9]{1,3}[-]?[0-9]{6,8}$/", $str)) {
        return true;
    }
    else {
        $str_message = '請輸入正確的電話格式';
        return false;
    }
}


//=================== CURL ============================
function post_CURL($url, $data, $headers = "", $debug = false, $CA = false, $CApem = "", $timeout = 30) {
     //網址,資料,header,返回錯誤訊息,https時驗證CA憑證,CA檔名,超時(秒)
    global $path_cacert;
    $result = "";
    $cacert = $path_cacert . $CApem;
     //CA根证书
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    if ($SSL && $CA && $CApem == "") {
        return "請指定CA檔名";
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
     //允許執行的最長秒數
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout - 2);
     //連接前等待時間(0為無限)
    $headers == '' ? '' : curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($SSL && $CA) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
         // 驗證CA憑證
        curl_setopt($ch, CURLOPT_CAINFO, $cacert);
         // CA憑證檔案位置
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    }
    else if ($SSL && !$CA) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         // 信任任何憑證
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);

    if ($debug === true && curl_errno($ch)) {
        echo 'GCM error: ' . curl_error($ch);
    }
    curl_close($ch);
    return $result;
}

function get_CURL($URL) {
    $result = "";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);

    //curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     //關閉SSL協議
    $result = curl_exec($ch);
    var_dump($result);
    var_dump(curl_error($ch));
    curl_close($ch);
    return $result;
}
function print_var_name($var) {
    foreach($GLOBALS as $var_name => $value) {
        if ($value === $var) {
            return $var_name;
        }
    }

    return false;
}
?>