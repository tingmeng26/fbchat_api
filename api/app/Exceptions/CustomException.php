<?php
namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    private $status_code;
    public function __construct($message, $code = 0, $status_code = 0){
        $this->status_code = $status_code;
    	parent::__construct($message, $code);
    }

    public function getStatusCode(){
    	return $this->status_code;
    }

    //錯誤碼 6碼補0
    public function getCustomCode(){
    	return str_pad($this->code,6,'0',STR_PAD_LEFT);
    }

    public function getCustomMessage(){
    	return $this->message;
    }
}
