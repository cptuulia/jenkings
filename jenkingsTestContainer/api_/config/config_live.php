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
        'host' => 'localhost',
        'database' => 'tuulia_pdfEncrypt',
        'username' => 'TODO',
        'password' => 'TODO',
    ],
    'api_host' => 'localhost',

    'admin' => [
        'passwordSalt' => 'Example',
        'api_key_expires' => 900, //seconds = 15 min
    ],
    'frontEndHost' => 'https://pdfencrypt.tantonius.com',
    'mailData' => [
        'fromEmail' => 'info@pdf_encrypt.nl',
        'fromName' => 'De Prins Willem-Alexander Manege',
        'replyEmail' => 'info@pdf_encrypt.nl',
        'replyName' => 'De Prins Willem-Alexander Manege',
        'testEmail' => 'pwamange@tantonius.com',
    ],
    'liveServer' => 'pdfencrypt.tantonius.com',
    'setLogStatemnts' => false,
];

require __DIR__ . '/authorizations/list.php';

return $params;
