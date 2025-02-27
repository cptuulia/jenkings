<?php

/*
 * NOTE: The following files are also to be updated
 *
 * api_/config/configTest.php
 * api_/config/config.php
 *
 *
 */

$params = [
    'db' => [
        'host' => 'pdf_encrypt_db',
        'database' => 'pdf_encrypt_db_name',
        'username' => 'root',
        'password' => 'root',
    ],
    'api_host' => 'pdf_encrypt_nginx',

    'admin' => [
        'passwordSalt' => 'Example',
        'api_key_expires' => 900, //seconds = 15 min
    ],
    'frontEndHost' => 'http://localhost:5173',
    'mailData' => [
        'fromEmail' => 'info@pdf_encrypt.nl',
        'fromName' => 'De Prins Willem-Alexander Manege',
        'replyEmail' => 'info@pdf_encrypt.nl',
        'replyName' => 'De Prins Willem-Alexander Manege',
        'testEmail' => 'pwamange@tantonius.com',
    ],
    'liveServer' => 'aanmelden.pdf_encrypt.nl',
    'setLogStatemnts' => false,
];

require __DIR__ . '/authorizations/list.php';

return $params;
