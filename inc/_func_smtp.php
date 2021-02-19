<?php
/**
 * @param $fr_em : 寄件者信箱
 * @param $fr_na : 寄件者姓名
 * @param $to_array : 收件者 array('email'=>'收件者信箱', 'name'=>'收件者姓名')
 * @param $subject : 寄件主旨
 * @param $msg : 寄件內容
 * @param bool $showerror : 是否顯示錯誤訊息
 * @param bool $mulAddBCC : 是否密件副本
 * @param array $attachment : 附件
 * @throws Exception
 */
function send_smtp2($fr_em, $fr_na, $to_array, $subject, $msg, $showerror = false, $mulAddBCC = false, $attachment = array())
{
    require_once('PHPMailer-master/PHPMailerAutoload.php');
    mb_internal_encoding('UTF-8');
    $mail = new PHPMailer($showerror);

    try {
        if ($showerror) {
            $mail->SMTPDebug = 0;
        }
        if (isset($GLOBALS["smtp_isSMTP"]) && $GLOBALS["smtp_isSMTP"]) {
            $mail->isSMTP();
        }
        $mail->Host = $GLOBALS["smtp_host"]; // SMTP servers
        $mail->Port = $GLOBALS["smtp_port"]; //default is 25, gmail is 465 or 587
        $mail->SMTPAuth = $GLOBALS["smtp_auth"]; //turn on SMTP authentication
        if ($GLOBALS["smtp_auth"]) {
            $mail->Username = $GLOBALS["smtp_id"]; // SMTP username
            $mail->Password = $GLOBALS["smtp_pw"]; // SMTP password
        }
        if (isset($GLOBALS["smtp_secure"]) && $GLOBALS["smtp_secure"] != '') {
            $mail->SMTPSecure = $GLOBALS["smtp_secure"];
        }

        $mail->setFrom($fr_em, $fr_na);

        $i = 0;
        foreach ($to_array as $row) {
            if ($mulAddBCC) {
                $mail->addBCC($row['email'], $row['name']);
            } else {
                $mail->addAddress($row['email'], $row['name']);
            }
            $i++;
        }

        //執行 $mail->AddAttachment() 加入附件，可以多個附件
        if (count($attachment) > 0) {
            foreach ($attachment as $value) {
                $mail->addAttachment($value);
            }
        }

        //電郵內容，以下為發送 HTML 格式的郵件
        $mail->CharSet = "utf-8";
        $mail->Encoding = "base64";
        $mail->isHTML(true); //send as HTML
        $mail->Subject = $subject;
        $mail->Body = $msg;

        $r = $mail->send();
        return $r;
    } catch (Exception $e) {
        if ($showerror) {
            throw new Exception('寄件失敗!' . $mail->ErrorInfo);
        }
    }
}

function send_smtp($fr_em, $fr_na, $to_ary, $subject, $msg, $attachment = array(), $showerror = true)
{
    require_once('PHPMailer-master/PHPMailerAutoload.php');
    mb_internal_encoding('UTF-8');

    $mail = new PHPMailer($showerror);
    //$mail->SMTPDebug = 3;
    if ($GLOBALS["smtp_isSMTP"]) {
        $mail->IsSMTP();
    }
    $mail->Host = $GLOBALS["smtp_host"];  // SMTP servers
    $mail->Port = $GLOBALS["smtp_port"];  //default is 25, gmail is 465 or 587
    $mail->SMTPAuth = $GLOBALS["smtp_auth"]; // turn on SMTP authentication
    if ($GLOBALS["smtp_auth"]) {
        $mail->Username = $GLOBALS["smtp_id"];    // SMTP username
        $mail->Password = $GLOBALS["smtp_pw"];    // SMTP password
    }
    if ($GLOBALS["smtp_secure"] != '') {
        $mail->SMTPSecure = $GLOBALS["smtp_secure"];
    }
    $mail->Sender = $GLOBALS["sys_email"];

    foreach ($to_ary as $row) {
        if (!empty($row['name']) && !empty($row['email'])) {
            $mail->AddAddress($row['email'], $row['name']);
        }
    }
    $mail->SetFrom($fr_em, $fr_na);
    //$mail->AddReplyTo("jyu@aemtechnology.com","AEM");
    //$mail->WordWrap = 50; // set word wrap

    // 電郵內容，以下為發送 HTML 格式的郵件
    $mail->CharSet = "utf-8";
    $mail->Encoding = "base64";
    $mail->IsHTML(true); // send as HTML
    $mail->Subject = $subject;
    $mail->Body = $msg;

    foreach ($attachment as $row) {
        $filename = $row['path'] . $row['file'];
        if ($filename != '' && is_file($filename)) {
            $mail->AddAttachment($filename);
        }
    }
    //$mail->AltBody = "This is the text-only body";

    $result = $mail->Send();
    if (!$result && $showerror) {//失敗
        throw new Exception('寄件失敗!' . $mail->ErrorInfo);
    }
    $mail->ClearAddresses();
    $mail->ClearAttachments();
}

function sendmail($fr_em, $fr_na, $to_em, $to_na, $subject, $msg)
{
    if ($to_em != '' && IsEmail($to_em)) {
        $recipient = $to_em;
        $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=\n";
        $mailheaders = "MIME-Version: 1.0\n";
        $mailheaders .= "Content-type: text/html; charset=utf-8\n";
        $from_name = "=?UTF-8?B?" . base64_encode($fr_na) . "?=";
        $mailheaders .= "From: " . $from_name . "<" . $fr_em . ">\n";
        if (!mail($recipient, $subject, $msg, $mailheaders)) {
            print_r(error_get_last());
            die ("無法送出mail!");
        }
    } else {
        echo "Email錯誤";
    }
}

function sendmail2($fr_em, $fr_na, $to_em, $to_na, $subject, $msg, $bcc = "")
{ //目前聯絡我使用
    //if($to_em != '' && IsEmail($to_em))
    if ($to_em != '') {
        //if($to_em != ''){
        $recipient = $to_em;
        $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=\n";
        $mail_headers = "MIME-Version: 1.0\n";
        $mail_headers .= "Content-type: text/html; charset=utf-8\n";
        $from_name = "=?UTF-8?B?" . base64_encode($fr_na) . "?=";
        $mail_headers .= "From: " . $from_name . "<" . $fr_em . ">\n";
        if ($bcc != "") {
            $mail_headers .= "Bcc:" . $bcc . "\n";
        }
        mail($recipient, $subject, $msg, $mail_headers) or die ("無法送出mail!");

    } else {
        echo "Email錯誤";
    }
}

function isValidEmail($address)
{
    // check an email address is possibly valid
    return preg_match('/^[a-z0-9.+_-]+@([a-z0-9-]+.)+[a-z]+$/i', $address);
}

function IsEmail($email)
{
    if (preg_match("/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/i", $email))
        return true;
    else
        return false;
}

?>
