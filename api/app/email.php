<?php
//發送email的lib
namespace App;
use Illuminate\Support\Facades\Mail;

class email{
	private $subject='';//主旨
	private $content_type='';//內容種類 font文字 or view
	private $view=null;//信件內容使用view view名
	private $view_data=null;//信件內容使用view view 資料
	private $content=null;//信件內容 文字
	private $recipient = [];//收件者(可多個)

	//信件主旨 $subject
	function __construct($subject){
		$this->subject = $subject;
	}

	//設置信件內容
	function setContent($vname,$vdata){
		$funary = func_num_args();
		if($funary==1){
			$this->content_type = 'font';
			$this->content = func_get_arg(0);
		}elseif($funary==2){//如果是兩個參數，視為設定view
			$this->content_type = 'view';
			$this->view = 'emails.'.func_get_arg(0);
			$this->view_data = func_get_arg(1);
		}
	}

	//設定收件者
	function setRecipient($recipient){
		if($recipient && !in_array($recipient,$this->recipient)){
			$this->recipient[] = $recipient;
		}
	}

	//發信
	function sendEmail(){
		$recipient = $this->recipient;
		$subject = $this->subject;
		switch ($this->content_type) {
			case 'font':
				$sent = Mail::raw($this->content, function ($msg) use ($recipient,$subject) {
						$msg->to($recipient);
						$msg->subject($subject);
					});
				break;
			case 'view':
				$sent = Mail::send($this->view, $this->view_data, function($msg) use ($recipient,$subject){
						    $msg->to($recipient);
							$msg->subject($subject);
						});
				break;
		}
	}
}
?>
