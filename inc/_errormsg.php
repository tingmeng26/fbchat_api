<?php
function getErrorMsg($type,$set = array()){
	$msg = '';
	$name = (isset($set['name'])?$set['name']:'');
    $maxlength = (isset($set['maxlength'])?$set['maxlength']:'');
    $minlenth = (isset($set['minlenth'])?$set['minlenth']:'');

    switch ($type) {
    	case 'require'://必填
            $msg = $name.'（必須）';
        break;
        case 'rangelength'://最大最小字數限制
            $msg = $name.'（'.($maxlength!=''?$maxlength.'字以内':'').($maxlength!='' && (int)$minlenth>=1?',':'').((int)$minlenth>=1?$minlenth."字以上":"").'）';
        break;
        case 'format'://格式錯誤 ex.圖片 email
            $msg = $name.'格式錯誤';
        break;
        case 'data_error'://傳入的數據非合法(超出範圍)
        	$name = ($name==''?'參數':$name);
            $msg = $name.' 錯誤';
        break;
        case 'data_repeat'://傳入的數據非合法(重複)
            $name = ($name==''?'參數':$name);
            $msg = $name.' 重複';
        break;
        case 'data_lost'://傳入的數據非合法(丟失)
            $name = ($name==''?'參數':$name);
            $msg = $name.' 遺失';
        break;
        case 'nodata'://查無相關資料
            $msg = '查無相關資料';
        break;
        case 'nologin'://未登入即進入須登入的頁面
            $msg = '請登入';
        break;
//自訂start
        case 'vaildImgCode_error':
            $msg = '驗證碼錯誤!';
        break;
        case 'search_nodata_login':
            $msg = '查無相關資料，請先註冊';
        break;
        case 'loginerror':
            $msg = '帳號或密碼錯誤!';
        break;
        case 'logindisable':
            $msg = '此帳號已停權!';
        break;
        case 'member_exi':
            $msg = '此電話已註冊';
        break;
//end
        case 'error'://未知錯誤
        default:
            $msg = 'error!';
        break;
    }
    return $msg;
}
?>