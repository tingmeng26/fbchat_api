<?php

namespace App;

class func {

    public function get_CURL($URL)
    {
        $result = "";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //關閉SSL協議
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function postCurl($url, $data, $headers = null, $debug = false, $CA = false, $CApem = "", $timeout = 30)
    {
        //網址,資料,header,返回錯誤訊息,https時驗證CA憑證,CA檔名,超時(秒)
        global $path_cacert;
        $result = "";
        $cacert = $path_cacert . $CApem;
        //CA根证书
        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        if ($SSL && $CA && $CApem == "") {
            return "請指定CA檔名";
        }
        if ($headers == null) {
            $headers = [
                'Content-Type: application/x-www-form-urlencoded',
            ];
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        //允許執行的最長秒數
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout - 2);
        //連接前等待時間(0為無限)
        //$headers == '' ? '' : curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($SSL && $CA) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            // 驗證CA憑證
            curl_setopt($ch, CURLOPT_CAINFO, $cacert);
            // CA憑證檔案位置
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } elseif ($SSL && !$CA) {
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

}

?>
