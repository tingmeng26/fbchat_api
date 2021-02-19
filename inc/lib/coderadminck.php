<?php
//一些常用的身分驗證
class coderadminck{
    public static function canViewUser($auth){//檢查$auth是否能瀏覽帳號列表頁
        global $adminuser;
        return coderAdmin::isInAuth($auth,coderAdmin::$Auth['admin']['key'],coderAdmin::$Auth['admin']['list']['admin']['key'],coderAdminLog::$action['view']['key']);
    }
}