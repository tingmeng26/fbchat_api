<?php
class class_password{
    /**
     * [取得加密後的密碼]
     * @param  [string] $password [未加密的密碼字串]
     * @return [string]           [加密後的字串(hash)]
     */
	public static function getEncryptPassWord($password) {
		return password_hash($password,PASSWORD_DEFAULT);
	}

    /**
     * [比對密碼是否匹配]
     * @param  [string] $password [要比對的密碼字串]
     * @param  [string] $hash     [由password_verify回傳的hash值]
     * @return [boolean]          [是否匹配]
     */
    public static function checkEncryptPassWord($password,$hash){
        return password_verify($password,$hash);
    }
}