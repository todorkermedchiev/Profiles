<?php

// ./index.php
// config, controllers, views

// стартиране на сесията
session_start();

// Добавяме конфигурация по подразбиране:
require_once 'config/defaults.php';

// Добавяме файл с функции:
require_once './functions.php';

// Проверяваме дали съществува ключа page в URL параметрите:
    $page = $_GET['page'] ?? DEFAULT_PAGE;

// Проверяваме дали съществува ключа action в URL параметрите:
$action = isset($_GET['action']) ? $_GET['action'] : DEFAULT_ACTION;
// $action = $_GET['action'] ?? DEFAULT_ACTION;

$routes = require_once 'config/routes.php';

require_once 'controllers/profiles.php';
require_once 'controllers/user.php';

// проверка дали $routes е масив, дали има ключ $page и дали стойността му е масив:
if (is_array($routes) && array_key_exists($page, $routes) && is_array($routes[$page])) {
    if (array_key_exists($action, $routes[$page]) && is_string($routes[$page][$action])) {
        $controller = $routes[$page][$action];
        // проверка дали цъществува такава функция:
        if (function_exists($controller)) {
            $viewModel = $controller();
        } else {
            http_response_code(500);
            die();
        }
    } else {
        http_response_code(404);
        die();
    }
} else {
    http_response_code(404);
    die();
}
// Проверяваме дали HTTP метода е GET:
if (method_is_get()) {
    $stringTpl = '%s/%s/%s.php';
    $viewFile = sprintf($stringTpl, VIEWS_DIR, $page, $action);
    //$viewFile = VIEWS_DIR . '/' . $page . '/' . $action . '.php';
    
    if (!file_exists($viewFile)) {
        http_response_code(500);
        die('No such view');
    }
    // Стартиране на буферирането на изхода:
    ob_start();
    // Добавяне файла с изгледа за конкретния контролер:
    require_once $viewFile;
    // Взимаме съдържанието на буфера:
    $content = ob_get_clean();
    // Спираме буферирането:
    ob_end_clean();
    if (!empty($_GET['warn'])) {
        $warn = (int) $_GET['warn'];
        $warningMessage = match($warn) {
            WARN_ALREADY_LOGGED => 'Already logged',
            default => 'Unknown warning ocuured',            
        };
    }
    
    if (!empty($_GET['err'])) {
        // Обръщаме $_GET['err'] към цяло число, защото всички стпйности от
        // $_GET, $_POST, $_REQUEST  и $_COOKIE идват като низове
        $err = (int) $_GET['err'];
        $errorMessage = match ($err) {
            ERR_MISSING_LOGIN_PARAM => 'Missing reqired parameter',
            ERR_INVALID_LOGIN_PARAM => 'Invalid parameter',
            ERR_REGISTER_MISS_FIRSTNAME => 'Missing first name',
            ERR_REGISTER_MISS_LASTNAME => 'Missing last name',
            ERR_REGISTER_MISS_EMAIL => 'Missing email',
            ERR_REGISTER_MISS_PASS => 'Missing password',
            ERR_REGISTER_MISS_CONFIRMPASS => 'Missing cofirmation of the password',
            ERR_REGISTER_MISS_TANDT => 'Please agree to our terms and conditions',
            ERR_REGISTER_CONFIRM_PASS => 'Wrong confirmed password',
            ERR_REGISTER_EMAIL_EXISTS => 'This email is already registered',
            default => 'Unknown error occured',
        };
    }
    
    if (!empty($_GET['success'])) {
        $success = (int) $_GET['success'];
        $warningMessage = match($success) {
            SUCCESS_REGISTER => 'Registration is successful',
            SUCCESS_LOGIN => 'Hello, ' . $_SESSION['user_name'],
            
        };
    }
    
    require_once VIEWS_DIR . '/layout.php';
}