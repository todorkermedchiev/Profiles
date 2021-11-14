<?php

// controllers/user.php

function user_login_controller() {
    if (user_is_logged()) {
        // Потребителят Е логнат
        // Пренасочване на потребителя към началната страница:
        redirect_to(DEFAULT_PAGE, DEFAULT_ACTION);
    }
    
    // Потребителят НЕ Е логнат
    // Проверяваме дали HTTP метода е GET
    if (method_is_get()) {
        // Показваме формата за вход...
        return [
            'pageTitle' => 'Users :: Login',
            'pageHeader' => 'Login',
        ];
    }
    
    process_login_form();
}

function process_login_form() {
    // METHOD == POST -> проверяваме стойностите от формата за вход:
    if (empty($_POST['email']) || empty($_POST['pass'])) {
        redirect_to('user', 'login', ['err' => ERR_MISSING_LOGIN_PARAM]);
    }

    // validation...
    if (strlen($_POST['pass']) < 6) {
        redirect_to('user', 'login', ['err' => ERR_INVALID_LOGIN_PARAM]);
    }

    $user = get_user_by_email($_POST['email']);
    if (!$user) {
        redirect_to('user', 'login', ['err' => ERR_INVALID_LOGIN_PARAM]);
    }

    if (password_verify($_POST['pass'], $user['password'])) {
        $_SESSION['logged'] = true;
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

        // Пренасочване към началната страница
        redirect_to(DEFAULT_PAGE, DEFAULT_ACTION, ['success' => SUCCESS_LOGIN]);
    }
        redirect_to('user', 'login', ['err' => ERR_INVALID_LOGIN_PARAM]);
}

/**
 * Loads user by email
 * 
 * @param string $email
 * 
 * @return array
 */
function get_user_by_email (string $email): array {
    $fileContent = file_get_contents('data/users');
    $allUsers = json_decode($fileContent, true);
    if (array_key_exists($email, $allUsers)) {
        return $allUsers[$email];
    }
    
    return [];
}

function user_register_controller() {
    /*
     * 1. Ако потребителят е логнат -> пренасочваме към началната страница с предупреждение
     * 2. Ако потребителят не е логнат ->
     * 2.1. Ако HTTP метода е GET -> показваме форматра за регистрация
     * 2.2. Ако HTTP матода е POST -> обработваме данните от формата
     */

    if (user_is_logged()) {
        // 1. ...
        redirect_to(DEFAULT_PAGE, DEFAULT_ACTION, ['warn' => WARN_ALREADY_LOGGED]);
    }
    
    // 2. ...
    if (method_is_get()) {
        // 2.1. ...
        return [
            'pageTitle' => 'Users :: Register',
            'pageHeader' => 'Register',
        ];
    }
    // 2.2. ...
    validate_register_form();

    if (get_user_by_email($_POST['email'])) {
        // Email-а вече е регистриран -> пренасочваме:
        redirect_to('user', 'register', ['err' => ERR_REGISTER_EMAIL_EXISTS]);
    }

    add_user([
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'password' => password_hash($_POST['pass'], PASSWORD_DEFAULT),
        'avatar' => upload_user_avatar(),
    ]);
    
    redirect_to('user', 'login', ['success' => SUCCESS_REGISTER]);
}

function add_user(array $user) {
    $fileContent = file_get_contents('data/users');
    $allUsers = json_decode($fileContent, true);
    $allUsers [$user['email']] = $user;
    file_put_contents('data/users', json_encode($allUsers, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function upload_user_avatar(): string {
    // проверка дали е качен файл в полето avatar
    if (empty($_FILES['avatar']) || is_array($_FILES['avatar'])) {
        return '';
    }
    
    // 1. Дали е възникнала грешка при качването
    if ($_FILES['avatar']['error']) {
        return '';
    }

    // 2. Дали е картинка - jpeg, png
    //$_FILES['avatar']['type']; // text/plain, image/jpeg (jpg, jpeg, jpe)
    $allowedTypes = ['image/jpeg', 'image/png'];
    if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
        return '';
    }

    // 3. Дали е под 2 Мб   
    //$_FILES['avatar']['size']; // bytes -> 1024 * 1024 * 2 
    $allowedSize = 1024 * 1024 * 2;
    if ($_FILES['avatar']['size'] > $allowedSize) {
        return '';
    }
    // data/avatars/md5($_POST['email'])
    if (is_uploaded_file($_FILES['avatar']['tmp_name'])) {
        $ext = ($_FILES['avatar']['type'] === 'image/jpeg') ? 'jpg' : 'png';
        $fileName = md5($_POST['email']);
        $path = 'data/avatars/' . $fileName . '.' . $ext;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path)) {
            return $path;
        }
        return '';
    }
    return '';
}

function validate_register_form() {
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
}

/**
 * Destroys the user's session and redirect to home page
 */
function user_logout_controller() {
    session_destroy();
    redirect_to('user', 'login');
}
