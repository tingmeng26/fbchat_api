<?php
require __DIR__.'/../../inc/_cusconfig.php';
return [
    'driver' => $env_mail_config['driver'],
    'host' => $env_mail_config['host'],
    'port' => $env_mail_config['port'],
    'encryption' => $env_mail_config['encryption'], // 同上 一般是tls或ssl
    'username' => $env_mail_config['username'],
    'password' => $env_mail_config['password'],
    'pretend' => $env_mail_config['pretend'],
    'from' => [
        'address' => $env_mail_config['from']['address'],
        'name' => $env_mail_config['from']['name']
    ],
    'stream' => [
        'ssl' => [
            'allow_self_signed' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ],
];
