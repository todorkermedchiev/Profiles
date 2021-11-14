<?php

// config/rputes.php

// page=user -> action = [login, register, forgot password, profile, log out]
// Пътищата до файловете са релативни (относителни), спрямо папката controllers

return [
    DEFAULT_PAGE => [
        DEFAULT_ACTION => 'profiles/list.php',
        'single' => 'profiles/single.php',
    ],
    'user' => [
        'login' => 'user/login.php',
        'register' => 'user/register.php',
        'logout' => 'user/logout.php'
    ]
];
