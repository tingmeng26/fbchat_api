<?php
class coderAES256{


	private static function addPkcs7Padding($string, $blocksize = 32) {
	    $len = strlen($string); //取得字符串长度
	    $pad = $blocksize - ($len % $blocksize); //取得补码的长度
	    $string .= str_repeat(chr($pad), $pad); //用ASCII码为补码长度的字符， 补足最后一段
	    return $string;
	}	

	private static function  aes256cbcEncrypt($str, $iv, $key ) {	
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, self::addPkcs7Padding($str) , MCRYPT_MODE_CBC, $iv));
	}


	private static function stripPkcs7Padding($string){
	    $slast = ord(substr($string, -1));
	    $slastc = chr($slast);
	    $pcheck = substr($string, -$slast);
	    if(preg_match("/$slastc{".$slast."}/", $string)){
	        $string = substr($string, 0, strlen($string)-$slast);
	        return $string;
	    } else {
	        return false;
	    }
	}


	private static function aes256cbcDecrypt($encryptedText, $iv, $key) {
		$encryptedText =base64_decode($encryptedText);
		return self::stripPkcs7Padding(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $encryptedText, MCRYPT_MODE_CBC, $iv));
	}

	private static function getKey(){
		return  hash( 'sha256', AES_KEY  , true );
	}

	public static function Encrypt($txt){
		$key = self::getKey();
		$iv=AES_IV;
		return self::aes256cbcEncrypt($txt, $iv, $key);
	}
	public static function Decrypt($txt){
		$key =  self::getKey();
		$iv=AES_IV;
		return self::aes256cbcDecrypt($txt, $iv, $key);
	}	
}

?>