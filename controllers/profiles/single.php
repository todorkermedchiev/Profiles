<?php

// controllers/profiles/single.php

if (empty($_SESSION['logged'])) {
    redirect_to('user', 'login');
}

if (empty($_GET['id'])) {
    header('Location: index.php');
    die();
}

$pageTitle = 'Single Profile';
$pageHeader = 'Profile';
$id = $_GET['id'];

$fp = fopen('data/profiles.csv', 'r');
$key  = fgetcsv($fp);
$profile = [];

while(($row = fgetcsv($fp)) !== false) {
    $combined = array_combine($key, $row);
    if ($combined['id'] === $id) {
        $profile = $combined;
        $birthdayTs = strtotime($profile['birthday']);
        $profile['birthday'] = date('d.m.Y', $birthdayTs);
        $pageHeader .= ' :: ' . $profile['first_name'] . ' ' . $profile['last_name'];
        break;
    }
}
fclose($fp);
