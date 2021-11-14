<?php

// functions.php

/*
 * функцията е изолиран блок с код който можем да извеждаме на различни места по име.
 * Името на функцията може да съдържа букви, цифри и долна черта, но не меже да
 * започва с цифра.
 */

/**
 * Create site URL
 * 
 * Create site URL by provided page, action and additional paramesters
 * 
 * @param string $page
 * @param string $action
 * @param array $params
 * 
 * @return string Built site URL
 */
function create_url(
        string $page = DEFAULT_PAGE,
        string $action = DEFAULT_ACTION,
        array $params = []
): string {
    $params['page'] = $page;
    $params['action'] = $action;
    $query = http_build_query($params);
    return 'index.php?' . $query;
}

/**
 * Redirect to URL
 * 
 * @param string $page
 * @param string $action
 * @param array $params
 * 
 * @return void
 */
function redirect_to(string $page, string $action, array $params = []): void {
    $url = create_url($page, $action, $params);
    header('Location: ' . $url);
    die();
}

/**
 * Checks whether the user is logged or not
 * 
 * @return bool True if the user is logged, otherwise false
 */
function user_is_logged(): bool {
    return !empty($_SESSION['logged']);
}

/**
 * Checks wether the reùest method is get or not
 * 
 * @return bool 
 */
function method_is_get(): bool {
    return strtolower($_SERVER['REQUEST_METHOD']) === 'get';
}