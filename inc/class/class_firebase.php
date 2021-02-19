<?php
class class_firebase{
	//發送訊息
	//$token 為接收者的token，可為字串(單一)，可為陣列(多人),主題(僅接受字串，多主題請使用in topics)
	//$message為訊息陣列 [
	//	'title'=>,
	//	'body'=>,
	//	'data'=>自訂陣列
	//]
	public static function sendMessage($token,$message) {
		global $inc_API_ACCESS_KEY;
		if(is_array($token)){
			$token = array_diff($token, array(null, 'null', '', ' '));
			$token = array_unique($token);
		}
		if(empty($token)){
			return false;
		}

		$url='https://fcm.googleapis.com/fcm/send';
		$msg = [
			'notification'=>[
				'title'  => isset($message['title'])?$message['title']:'',
				'body'  => isset($message['body'])?$message['body']:'',
				'sound' => 'default',
				'badge' =>  1
			]
		];
		if(isset($message['data'])){
			$msg['data'] = $message['data'];
		}

		$recipient = [];
		if(is_array($token) && count($token)>1){
			$recipient = array('registration_ids'=>$token);
		}else{
			if(is_array($token)){
				$token = $token[0];
			}
			if(preg_match("/in topics/",$token)){
				$recipient = array('condition'=>$token);
			}else{
				$recipient = array('to'=>$token);
			}
		}

		$fields = array_merge($recipient,$msg);

		$headers = array(
			'Authorization:key=' . $inc_API_ACCESS_KEY,
			'Content-Type:application/json'
		);
		$result = self::post_CURL($url,json_encode($fields), $headers);
		return $result;
	}

	private static function post_CURL($url, $data, $headers = "", $debug = false, $CA = false, $CApem = "", $timeout = 30) {
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
	    $options = array(
	      CURLOPT_URL=>$url,
	      //CURLOPT_TIMEOUT=>$timeout,//允許執行的最長秒數
	      //CURLOPT_CONNECTTIMEOUT=>$timeout - 2,//連接前等待時間(0為無限)
	      CURLOPT_HEADER=>0,
	      //CURLOPT_VERBOSE=>0,
	      CURLOPT_RETURNTRANSFER=>true,// false 時只回傳成功與否
	      //CURLOPT_USERAGENT=>"Mozilla/4.0 (compatible;)",
	      CURLOPT_POST=>true,
	      CURLOPT_POSTFIELDS=>(is_array($data)?http_build_query($data):$data),
	    );
	    if($headers != ''){
	        $options[CURLOPT_HTTPHEADER] = $headers;
	    }
	    if ($SSL && $CA) {
	        $options[CURLOPT_SSL_VERIFYPEER] = true;// 驗證CA憑證
	        $options[CURLOPT_CAINFO] = $cacert;// CA憑證檔案位置
	        $options[CURLOPT_SSL_VERIFYHOST] = 2;
	    }else if ($SSL && !$CA) {
	        $options[CURLOPT_SSL_VERIFYPEER] = false;// 信任任何憑證
	        $options[CURLOPT_SSL_VERIFYHOST] = 2;
	    }
	    curl_setopt_array($ch, $options);

	    $result = curl_exec($ch);
	    if ($debug === true && curl_errno($ch)) {
	        echo 'GCM error: ' . curl_error($ch);
	    }
	    curl_close($ch);
	    return $result;
	}
}
?>
