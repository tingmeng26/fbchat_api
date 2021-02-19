<?php
function exceptionErrorHandler($errno, $errstr, $errfile, $errline )
{
	 if (error_reporting()===0) {
        return;
    }
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exceptionErrorHandler");


class coderErrorHandle{
    private $_exceptions = array();
    private $_renderExceptions = true;
	private $_showinfo=true;
	public $reload=false;
    public function setException(Exception $e)
    {
        $this->_exceptions[] = $e;
    }
    public function getExceptions()
    {
        return $this->_exceptions;
    }
    public function isException()
    {
        return !empty($this->_exceptions);
    }
    public function renderExceptions($flag = null)
    {
        if (null !== $flag) {
            $this->_renderExceptions = $flag ? true : false;
        }
        return $this->_renderExceptions;
    }

	public function getErrorMessage($debug=null){
		$debug= $debug==null ? $this->_showinfo:$debug;
		$exception = '';
        if ($this->isException() && $this->renderExceptions()) {
            foreach ($this->getExceptions() as $e) {
					$exception.=$this->outputFormat($e,$debug);
            }
            return $exception;
        }
	}

    public function showError($debug=null)
    {
		$debug= $debug==null ? $this->_showinfo:$debug;
        $exception = '';
        if ($this->isException() && $this->renderExceptions()) {
            foreach ($this->getExceptions() as $e) {
				$exception.=$this->outputFormat($e,$debug);
            }
        }
        $this->showErrorPage($exception);

    }

	private function outputFormat($e,$debug=true){
		if($debug==true && $e->getCode()>0){
			return '[ID '.$e->getCode().'] '.$e->getMessage().' (Line: '.$e->getLine().' of '.$e->getFile().') ;';
		}
		else{
			return $e->getMessage().';';
		}
	}

	private function getBody(){
		return '<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/font-awesome/css/font-awesome.min.css">

        <!--flaty css styles-->
        <link rel="stylesheet" href="../css/flaty.css">
        <link rel="stylesheet" href="../css/flaty-responsive.css">
    </head>
	<body class="error-page"><div class="error-wrapper">
            <h5><img src="../images/logo.png" width="45%"><span>OOPS</span></h5><p><h5>Sorry!出了點小問題...</h5></p><p>
			[:content:]
	<h3>請將上述內容提供給系統管理員。</h3><hr/>
            <p class="clearfix">
                <a href="javascript:void(0)" onclick="window.location.href = document.referrer" class="pull-left">← 回到前一頁</a>
            </p>
			</div>
	 <!--basic scripts-->
        <script src="../assets/jquery/jquery-2.0.3.min.js"></script>
        <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>
			';
	}
    public function showErrorPage($exception){
        $body=$this->getBody();
        echo str_replace('[:content:]',$exception,$body);
        exit;
    }

}

?>