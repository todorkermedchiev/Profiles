<?php

// controllers/files/list.php

if (empty($_SESSION['logged'])) {
    // потребителят не е логнат
    redirect_to('user', 'login');
} else {
    // Потребителят е логнат 
    $pageTitle = 'Home :: Profile list';
    $pageHeader = 'Profile list';
    /*
     * 1. Отваряме csv файла с профили
     * 2. Да превърнем информацията в двумерен масив
     * 3. Да сортираме профилите по малко име
     * 4. Странициране на резултатите
     */   
    
    $profiles = [];
    
    // 1.
    // отваряме ресурс за четене на файла:
    $fp = fopen('data/profiles.csv', 'r');
    // взимаме първия ред, който съдържа имената на колоните
    $keys = fgetcsv($fp);
    
    // 2.
    // Доакто fgetcsv дава резултат, различен от false:
    while (($row = fgetcsv($fp)) !== false) {
        // Комбинираме ключовете със стойностите :
        $profile = array_combine($keys, $row);
        // Преформатираме рождения ден:
        $birthdayTs = strtotime($profile['birthday']);
        $profile['birthday'] = date('d.m.Y', $birthdayTs); // 04.06.2000
        // и ги добавяме като нов профил:
        $profiles[] = $profile;
    }
    // затваряме ресурса
    fclose($fp);
    
    // 3.
    /*
     * $profiles = [
     *      [
     *          'first_name' => '...',
     *          'last_name' => '...',
     *          ...
     *      ],
     *      [
     *          'first_name' => '...',
     *          'last_name' => '...',
     *          ...
     *      ],
     *      ...
     * ]
     */
    // Функция за ставнение
    $compareCallback = function($a, $b) {
        // -1, 0, 1
        return $a['first_name'] <=> $b['first_name'];
    };
    usort($profiles, $compareCallback);
    
    // 4.
    // елементи на страница - ако не е посочен брой в адрес даваме 10
    $itemsPerPage = (int) ($_GET['perPage'] ?? 10);
    // Ткуща транице - ако не е посочен в адреса даваме 1:
    $currentPage = (int) ($_GET['currentPage'] ?? 1);
    // Брой страници:
    // Закръгляне към цяло число нагоре:
    $totalPages = ceil(count($profiles) / $itemsPerPage);
    // Начален ингекс от масива с данни:
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    $profiles = array_slice($profiles, $offset, $itemsPerPage);
}


