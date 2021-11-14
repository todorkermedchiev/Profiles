<?php

// controller/user/register.php

/*
 * 1. Ако потребителят е логнат -> пренасочваме към началната страница с предупреждение
 * 2. Ако потребителят не е логнат ->
 * 2.1. Ако HTTP метода е GET -> показваме форматра за регистрация
 * 2.2. Ако HTTP матода е POST -> обработваме данните от формата
 */

if (!empty($_SESSION['logged'])) {
    // 1. ...
    header('Location: index.php?warn=' . WARN_ALREADY_LOGGED);
    die();
} else {
    // 2. ...
    if (strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
        // 2.1. ...
        $pageTitle = 'Users :: Register';
        $pageHeader = 'Register'; 
    } else {
        // 2.2. ...
        // Проверка дали съществува стойност за име:
        if (empty($_POST['first_name'])) {
            redirect_to('user', 'register', ['err' => ERR_REGISTER_MISS_FIRSTNAME]);
        }
         // Проверка дали съществува стойност за Фамилно име:
        if (empty($_POST['last_name'])) {
            redirect_to('user', 'register', ['err' => ERR_REGISTER_MISS_LASTNAME]);
            }
        // Проверка дали съществува стойност за имейл:
        if (empty($_POST['email'])) {
            redirect_to('user', 'register', ['err' => ERR_REGISTER_MISS_EMAIL]);
        }
        // Проверка дали съществува стойност за парола:
        if (empty($_POST['pass'])) {
            redirect_to('user', 'register', ['err' => ERR_REGISTER_MISS_PASS]);
        }
        // Проверка дали съществува стойност за потвърждение на парола:
        if (empty($_POST['confirm_pass'])) {
            redirect_to('user', 'register', ['err' => ERR_REGISTER_MISS_CONFIRMPASS]);
        }
        // Проверка дали съществува стойност за terms & conditions:
        if (empty($_POST['t_and_t'])) {
            redirect_to('user', 'register', ['err' => ERR_REGISTER_MISS_TANDT]);
        }
        // проверка дали паролата е парвилно потвърдена:
        if ($_POST['pass'] !== $_POST['confirm_pass']) {
            redirect_to('user', 'register', ['err' => ERR_REGISTER_CONFIRM_PASS]);
        }
        
        // regex validations
        /*
         * 1. Проверка дали имейлът е вече регистриран? Ако не е -> пренасочване...
         * 2. Ако не е ->
         * 2.1. събираме данните в асоц. масив
         * 2.2. добавяме ги към списъка с потребители от data/users
         * 2.3. записваме данните във файла
         */
        
        /*
         * $allUsers = [
         *      'j.doe@example.com' => [
         *          'first_name' => '...',
         *          'last_name' => '...',
         *          'avatar' => '...',
         *          'passwprd' => '...',
         *      },
         *      'jane.doe@example.com' => [
         *          'first_name' => '...',
         *          'last_name' => '...',
         *          'avatar' => '...',
         *          'password' => '...',
         *      },
         * ]
         */
        
        /*
         * JSON - JavaScript Object Naotation
         */
        // 1.
        $filecontent = file_get_contents('data/users');
        $allUsers = json_decode($filecontent, true);
        if (array_key_exists($_POST['email'], $allUsers)) {
            // Email-а вече е регистриран -> пренасочваме:
            redirect_to('user', 'register', ['err' => ERR_REGISTER_EMAIL_EXISTS]);
        }        
        
        // 2.1.
        $user = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'password' => password_hash($_POST['pass'], PASSWORD_DEFAULT),
            'avatar' => '',
        ];
        
        // 2.3. Avarar processing ...
        // проверка дали е качен файл в полето avatar
        if (!empty($_FILES['avatar']) && is_array($_FILES['avatar'])) {
            // 1. Дали е възникнала грешка при качването
            if ($_FILES['avatar']['error']) {
                // ...
            }      
            
            // 2. Дали е картинка - jpeg, png
            //$_FILES['avatar']['type']; // text/plain, image/jpeg (jpg, jpeg, jpe)
            $allowedTypes = ['image/jpeg', 'image/png'];
            if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
                // ...
            }
            
            // 3. Дали е под 2 Мб   
            //$_FILES['avatar']['size']; // bytes -> 1024 * 1024 * 2 
            $allowedSize = 1024 * 1024 * 2;
            if ($_FILES['avatar']['size'] > $allowedSize) {
                // ...
            }
            // data/avatars/md5($_POST['email'])
            if (is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                if ($_FILES['avatar']['type'] === 'image/jpeg') {
                    $ext = 'jpg';
                } else {
                    $ext = 'png';
                }
                $fileName = md5($_POST['email']);
                $path = 'data/avatars/' . $fileName . '.' . $ext;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path)) {
                    $user['avatar'] = $path;
                }
            }
        }      
            
        // 2.2.
        $allUsers[$_POST['email']] = $user;        
        file_put_contents('data/users', json_encode($allUsers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        
        redirect_to('user', 'login', ['success' => SUCCESS_REGISTER]);
    }
}
