<?php
class coderLang
{
    private static $_lang='';
    private static $_lang_back=''; //後台用
    private static $_def_lang='zh-hant';
    private static $_cookie_name='yangs_site_lang';
    private static $_cookie_name_back='yangs_site_lang_back'; //後台用
    public static $_lang_ary=array('zh-hant'=>'繁體中文','zh-hans'=>'简体中文','en'=>'ENGLISH');
    public static $_dic=null;
    public static function getDic(){
        if(self::$_dic!=null){
            return self::$_dic;
        }
        $lang=self::get();
        self::confDic($lang);
        return self::$_dic;
    }
    private static function confDic($lang){
        $path=CONFIG_DIR.'lang/'.$lang.'.php';
        if(!file_exists ($path)){
            $path=CONFIG_DIR.'lang/'.self::$_def_lang.'.php';
        }
        include_once($path);
        self::$_dic=$_dic_lang;
    }
    public static function set($lang)
    {
        global $web_root;
        if(array_key_exists( $lang, self::$_lang_ary ) ){
            coderWebHelp::saveCookie(self::$_cookie_name, $lang,  $web_root, $httponly=true);
            self::$_lang=$lang;
        }
    }
    public static function get(){
        if(self::$_lang!='' && array_key_exists(self::$_lang, self::$_lang_ary)){
            return self::$_lang;
        }
        if(get('lang',1)!=''){
            $lang=get('lang',1);
        }else{
            $lang=coderWebHelp::getCookie(self::$_cookie_name);
        }
        if(trim($lang)=="" || !array_key_exists($lang,  self::$_lang_ary)){
            self::set(self::$_def_lang);
            $lang=self::$_def_lang;
        }
        return $lang;
    }

    //後台用
    public static function back_set($lang)
    {
        global $webmanageurl_cookiepath;
        if(array_key_exists( $lang, self::$_lang_ary ) ){
            coderWebHelp::saveCookie(self::$_cookie_name_back, $lang,  $webmanageurl_cookiepath, $httponly=true);
            self::$_lang_back=$lang;
        }
    }
    //後台用
    public static function back_get(){
        if(self::$_lang_back!='' && array_key_exists(self::$_lang_back, self::$_lang_ary)){
            return self::$_lang_back;
        }
        if(get('def_lang',1)!=''){
            $lang=get('def_lang',1);
        }else{
            $lang=coderWebHelp::getCookie(self::$_cookie_name_back);
        }
        if(trim($lang)=="" || !array_key_exists($lang,  self::$_lang_ary)){
            self::back_set(self::$_def_lang);
            $lang=self::$_def_lang;
        }
        return $lang;
    }

}