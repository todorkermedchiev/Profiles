<?php

// config/rputes.php

// page=user -> action = [login, register, forgot password, profile, log out]
// Пътищата до файловете са релативни (относителни), спрямо папката controllers

return [
    DEFAULT_PAGE => [
        DEFAULT_ACTION => 'profiles_list_controller',
        'single' => 'profiles_single_controller',
    ],
    'user' => [
        'login' => 'user_login_controller',
        'register' => 'user_register_controller',
        'logout' => 'user_logout_controller'
    ]
];
