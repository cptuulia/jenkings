<?php
//////////////////////////////////////////////////////////////
// quest pages
//////////////////////////////////////////////////////////////
$params['guestPages'] = [
    'POST' => [],
    'PUT' => [],
    'GET' => [],
    'DELETE' => [],
];


$params['guestPages']['GET'][] = '/api/test';
$params['guestPages']['GET'][] = '/api';

$params['guestPages']['POST'][] = '/api/admin/users';
$params['guestPages']['POST'][] = '/api/admin/users/login';
$params['guestPages']['POST'][] = '/api/admin/users/logout/(\d+)';

