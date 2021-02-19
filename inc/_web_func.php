<?php
function redirect_404(){
    header("HTTP/1.1 404 Not Found");
    //header("Refresh: 0; url=/404.php");
    exit;
}


function set_emailsample($body){//設置email的樣板
    $html = '';
    $html .= '<!doctype html>
    <head></head>
    <body>
        '.$body.'
    </body>
    </html>
    ';

    return $html;
}
?>